<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\Request as RequestModel; // Alias to avoid conflict with Illuminate\Http\Request
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\Inventory; // Thêm model Inventory

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:supplier');
    }

    // Dashboard
    public function dashboard()
    {
        $totalProducts = Product::where('supplier_id', Auth::id())->count();
        $pendingOrders = Order::where('status', 'pending')
            ->whereHas('orderItems.product', function ($query) {
                $query->where('supplier_id', Auth::id());
            })->count();
        $monthlyRevenue = Order::where('status', 'completed')
            ->whereHas('orderItems.product', function ($query) {
                $query->where('supplier_id', Auth::id());
            })
            ->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->sum('total_amount');

        return view('supplier.dashboard', compact('totalProducts', 'pendingOrders', 'monthlyRevenue'));
    }

    // Danh sách sản phẩm
    public function products(Request $request)
    {
        $query = Product::where('supplier_id', Auth::id());

        if ($request->input('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->input('search') . '%')
                  ->orWhere('sku', 'like', '%' . $request->input('search') . '%');
            });
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('supplier.products.index', compact('products'));
    }

    // Tạo sản phẩm mới
    public function createProduct()
    {
        return view('supplier.products.create');
    }

    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'regular_price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'sku' => 'required|string|unique:products',
            'stock_quantity' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        $validated['supplier_id'] = Auth::id();
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        // Bắt đầu transaction để đảm bảo cả Product và Inventory được thêm thành công
        \DB::beginTransaction();
        try {
            // Tạo sản phẩm
            $product = Product::create($validated);

            // Tạo bản ghi trong inventories với stock từ stock_quantity
            Inventory::create([
                'product_id' => $product->id,
                'stock' => $validated['stock_quantity'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            \DB::commit();
            return redirect()->route('supplier.products')->with('success', 'Sản phẩm đã được thêm thành công!');
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra khi thêm sản phẩm: ' . $e->getMessage());
        }
    }

    // Sửa sản phẩm
    public function editProduct($id)
    {
        $product = Product::where('supplier_id', Auth::id())->findOrFail($id);
        return view('supplier.products.edit', compact('product'));
    }

    public function updateProduct(Request $request, $id)
    {
        $product = Product::where('supplier_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'regular_price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'sku' => 'required|string|unique:products,sku,' . $id,
            'stock_quantity' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image && Storage::exists('public/' . $product->image)) {
                Storage::delete('public/' . $product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        // Bắt đầu transaction để đảm bảo cả Product và Inventory được cập nhật
        \DB::beginTransaction();
        try {
            $product->update($validated);

            // Cập nhật hoặc tạo bản ghi trong inventories
            $inventory = Inventory::firstOrCreate(
                ['product_id' => $product->id],
                ['stock' => $validated['stock_quantity'], 'created_at' => now(), 'updated_at' => now()]
            );
            if ($inventory->wasRecentlyCreated === false) {
                $inventory->update(['stock' => $validated['stock_quantity'], 'updated_at' => now()]);
            }

            \DB::commit();
            return redirect()->route('supplier.products')->with('success', 'Sản phẩm đã được cập nhật thành công!');
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra khi cập nhật sản phẩm: ' . $e->getMessage());
        }
    }

    // Xóa sản phẩm
    public function destroyProduct($id)
    {
        $product = Product::where('supplier_id', Auth::id())->findOrFail($id);
        if ($product->image && Storage::exists('public/' . $product->image)) {
            Storage::delete('public/' . $product->image);
        }
        $product->delete();

        return redirect()->route('supplier.products')->with('success', 'Sản phẩm đã được xóa thành công!');
    }

    // Cập nhật tồn kho
    public function updateStock(Request $request, $id)
    {
        $product = Product::where('supplier_id', Auth::id())->findOrFail($id);
        $validated = $request->validate(['stock_quantity' => 'required|integer|min:0']);
        $product->update($validated);
        return redirect()->route('supplier.products')->with('success', 'Cập nhật tồn kho thành công!');
    }

    // Theo dõi đơn hàng
    public function orders(Request $request)
    {
        $query = Order::whereHas('orderItems.product', function ($query) {
            $query->where('supplier_id', Auth::id());
        });

        if ($request->input('search')) {
            $query->where('order_number', 'like', '%' . $request->input('search') . '%');
        }
        if ($request->input('status')) {
            $query->where('status', $request->input('status'));
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('supplier.orders.index', compact('orders'));
    }

    // Xem chi tiết đơn hàng
    public function showOrder($id)
    {
        $order = Order::with('orderItems.product')->findOrFail($id);
        if (!$order->orderItems->where('product.supplier_id', Auth::id())->count()) {
            abort(403, 'Bạn không có quyền xem đơn hàng này.');
        }
        return view('supplier.orders.show', compact('order'));
    }

    // Danh sách yêu cầu nhập hàng
    public function requests(Request $request)
    {
        $query = RequestModel::where('supplier_id', Auth::id());

        if ($request->input('search')) {
            $query->where('request_number', 'like', '%' . $request->input('search') . '%');
        }
        if ($request->input('status')) {
            $query->where('status', $request->input('status'));
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('supplier.requests.index', compact('requests'));
    }

    // Xử lý yêu cầu nhập hàng
    public function processRequest(Request $request, $id)
    {
        $req = RequestModel::where('supplier_id', Auth::id())->findOrFail($id);
        $validated = $request->validate(['status' => 'required|in:accepted,rejected']);
        $req->update($validated);
        return redirect()->route('supplier.requests')->with('success', 'Xử lý yêu cầu thành công!');
    }
}
