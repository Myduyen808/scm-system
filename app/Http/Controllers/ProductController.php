<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Inventory;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

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
        // Chỉ admin tự động phê duyệt khi thêm mới
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

        // Nếu Supplier hoặc Employee thêm, gửi message RabbitMQ để thông báo cần approve
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

    // Approve sản phẩm (cho Admin và Employee)
    public function approve($id)
    {
        $this->authorize('approve products');

        $product = Product::findOrFail($id);
        $product->is_approved = true;
        $product->is_active = true;
        $product->save();

        // Gửi thông báo qua RabbitMQ khi phê duyệt (bỏ comment nếu cần)
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

    // Danh sách sản phẩm chờ approve (cho Admin và Employee)
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

    // Danh sách sản phẩm đã phê duyệt (cho Admin và Employee)
    public function approvedProducts()
    {
        $this->authorize('approve products');
        $approvedProducts = Product::where('is_approved', true)->with('supplier', 'inventory')->paginate(10);
        return view('employee.approved.products', compact('approvedProducts'));
    }
}
