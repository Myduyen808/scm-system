<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Inventory;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    protected function getRabbitMQConnection()
    {
        return new AMQPStreamConnection(
            env('RABBITMQ_HOST', 'localhost'),
            env('RABBITMQ_PORT', 5672),
            env('RABBITMQ_USER', 'guest'),
            env('RABBITMQ_PASSWORD', 'guest')
        );
    }

    // Hiển thị danh sách tất cả sản phẩm (tùy vai trò)
    public function index()
    {
        $user = auth()->user();
        $role = $user->roles->first()->name;

        if ($role === 'admin') {
            $products = Product::with('supplier', 'inventory')->paginate(10);
        } elseif ($role === 'supplier') {
            $products = Product::where('supplier_id', $user->id)->with('inventory')->paginate(10);
        } else {
            $products = Product::where('is_approved', true)->with('inventory')->paginate(10);
        }

        return view('products.index', compact('products'));
    }

    // Hiển thị chi tiết sản phẩm
    public function show($id)
    {
        $product = Product::with('inventory', 'supplier')->findOrFail($id);
        return view('products.show', compact('product'));
    }

    // Thêm sản phẩm mới
    public function store(Request $request)
    {
        $user = auth()->user();
        $role = $user->roles->first()->name;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'regular_price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'sku' => 'required|string|unique:products',
            'stock_quantity' => 'required|integer|min:0',
        ]);

        $product = new Product($validated);
        $product->supplier_id = ($role === 'supplier') ? $user->id : null;
        $product->is_approved = ($role === 'admin');
        $product->save();

        if ($request->hasFile('image')) {
            $product->image = $request->file('image')->store('products', 'public');
            $product->save();
        }

        Inventory::create([
            'product_id' => $product->id,
            'stock' => $validated['stock_quantity'],
        ]);

        if ($role === 'supplier' || $role === 'employee') {
            $connection = $this->getRabbitMQConnection();
            $channel = $connection->channel();
            $channel->queue_declare('product_approval', false, true, false, false);

            $msg = new AMQPMessage(json_encode(['product_id' => $product->id, 'action' => 'new_product']));
            $channel->basic_publish($msg, '', 'product_approval');

            $channel->close();
            $connection->close();
        }

        return redirect()->back()->with('success', 'Sản phẩm đã được thêm!');
    }

    // Cập nhật thông tin sản phẩm
    public function update(Request $request, $id)
    {
        $this->authorize('update', Product::class);

        $product = Product::findOrFail($id);
        $user = auth()->user();
        $role = $user->roles->first()->name;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'regular_price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'sku' => 'required|string|unique:products,sku,' . $id,
            'stock_quantity' => 'required|integer|min:0',
        ]);

        $product->fill($validated);
        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $product->image = $request->file('image')->store('products', 'public');
        }
        $product->save();

        $inventory = $product->inventory;
        $inventory->stock = $validated['stock_quantity'];
        $inventory->save();

        if ($role === 'supplier' || $role === 'employee') {
            $connection = $this->getRabbitMQConnection();
            $channel = $connection->channel();
            $channel->queue_declare('product_update', false, true, false, false);

            $msg = new AMQPMessage(json_encode(['product_id' => $product->id, 'action' => 'updated']));
            $channel->basic_publish($msg, '', 'product_update');

            $channel->close();
            $connection->close();
        }

        return redirect()->route('products.show', $product->id)->with('success', 'Sản phẩm đã được cập nhật!');
    }

    // Xóa sản phẩm
    public function destroy($id)
    {
        $this->authorize('delete', Product::class);

        $product = Product::findOrFail($id);
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $product->inventory()->delete();
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Sản phẩm đã được xóa!');
    }

    // Phê duyệt sản phẩm
    public function approve($id)
    {
        $this->authorize('approve products');

        $product = Product::findOrFail($id);
        $product->is_approved = true;
        $product->is_active = true;
        $product->save();

        /*
        $connection = $this->getRabbitMQConnection();
        $channel = $connection->channel();
        $channel->queue_declare('product_update', false, true, false, false);

        $msg = new AMQPMessage(json_encode(['product_id' => $product->id, 'action' => 'approved']));
        $channel->basic_publish($msg, '', 'product_update');

        $channel->close();
        $connection->close();
        */

        return redirect()->back()->with('success', 'Sản phẩm đã được phê duyệt và hiển thị cho khách hàng!');
    }

    // Danh sách sản phẩm chờ approve
    public function pendingProducts()
    {
        $this->authorize('approve products');
        $pendingProducts = Product::where('is_approved', false)->with('supplier', 'inventory')->get();
        $approvedProducts = Product::where('is_approved', true)
            ->with('supplier', 'inventory')
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();
        return view('admin.pending.products', compact('pendingProducts', 'approvedProducts'));
    }

    // Danh sách sản phẩm đã phê duyệt
    public function approvedProducts()
    {
        $this->authorize('approve products');
        $approvedProducts = Product::where('is_approved', true)->with('supplier', 'inventory')->paginate(10);
        return view('employee.approved.products', compact('approvedProducts'));
    }
}
