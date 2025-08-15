<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash; // vì bạn có dùng Hash::make()
use Spatie\Permission\Models\Role;   // thêm dòng này
use Illuminate\Support\Facades\Storage; // vì bạn có dùng Storage::exists()
use App\Models\Setting;


class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    // ==================== DASHBOARD ====================
    public function dashboard()
    {
        // Thống kê tổng quan
        $totalProducts = Product::count();
        $totalOrders = Order::count();
        $totalUsers = User::count();
        $totalRevenue = Order::where('payment_status', 'paid')->sum('total_amount');

        // Đơn hàng mới (hôm nay)
        $todayOrders = Order::whereDate('created_at', today())->count();

        // Sản phẩm sắp hết hàng (< 10 sản phẩm)
        $lowStockProducts = Product::where('stock_quantity', '<', 10)->count();

        // Top 5 sản phẩm bán chạy
        $topProducts = Product::withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->take(5)
            ->get();

        // Đơn hàng gần đây
        $recentOrders = Order::with('customer')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalProducts', 'totalOrders', 'totalUsers', 'totalRevenue',
            'todayOrders', 'lowStockProducts', 'topProducts', 'recentOrders'
        ));
    }

        // ==================== QUẢN LÝ KHO ====================
    public function inventory(Request $request)
    {
        $query = Product::with('supplier');

        // Tìm kiếm
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%');
        }

        // Lọc theo trạng thái
        if ($request->status) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'low_stock') {
                $query->where('stock_quantity', '<', 10);
            }
        }

        // Lọc theo supplier
        if ($request->supplier) {
            $query->where('supplier_id', $request->supplier);
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(12);
        $suppliers = User::role('supplier')->get();

        return view('admin.inventory.index', compact('products', 'suppliers'));
    }

    public function createProduct()
    {
        $suppliers = User::role('supplier')->get();
        return view('admin.inventory.create', compact('suppliers'));
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
            'supplier_id' => 'nullable|exists:users,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        // Upload hình ảnh
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($validated);

        return redirect()->route('admin.inventory')
                        ->with('success', 'Sản phẩm đã được thêm thành công!');
    }

    // Hiển thị form edit sản phẩm
    public function editProduct($id)
    {
        $product = Product::findOrFail($id);
        $suppliers = User::role('supplier')->get();
        return view('admin.inventory.edit', compact('product', 'suppliers'));
    }

    // Update sản phẩm
    public function updateProduct(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'regular_price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'sku' => 'required|string|unique:products,sku,' . $id,
            'stock_quantity' => 'required|integer|min:0',
            'supplier_id' => 'nullable|exists:users,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        // Upload hình ảnh nếu có
        if ($request->hasFile('image')) {
            // Xóa ảnh cũ nếu tồn tại
            if ($product->image && Storage::exists('public/' . $product->image)) {
                Storage::delete('public/' . $product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        return redirect()->route('admin.inventory')
                        ->with('success', 'Sản phẩm đã được cập nhật thành công!');
    }

    // Delete sản phẩm
    public function destroyProduct($id)
    {
        $product = Product::findOrFail($id);

        // Xóa ảnh nếu tồn tại
        if ($product->image && Storage::exists('public/' . $product->image)) {
            Storage::delete('public/' . $product->image);
        }

        $product->delete();

        return redirect()->route('admin.inventory')
                        ->with('success', 'Sản phẩm đã được xóa thành công!');
    }

    // Update stock nhanh (đã có trong route, thêm vào controller)
    public function updateStock(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'stock_quantity' => 'required|integer|min:0',
        ]);

        $product->update($validated);

        return response()->json(['message' => 'Cập nhật tồn kho thành công!']);
    }

    // ==================== QUẢN LÝ ĐƠN HÀNG ====================
    public function orders(Request $request)
    {
        $query = Order::with('customer', 'orderItems.product');

        // Tìm kiếm và lọc (tương tự inventory)
        if ($request->search) {
            $query->where('order_number', 'like', '%' . $request->search . '%');
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.orders.index', compact('orders'));
    }

    public function showOrder($id)
    {
        $order = Order::with('customer', 'orderItems.product')->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $validated = $request->validate(['status' => 'required|in:pending,processing,shipped,delivered,cancelled']);
        $order->update($validated);
        return redirect()->back()->with('success', 'Cập nhật trạng thái thành công!');
    }

    public function cancelOrder($id)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => 'cancelled']);
        // Hoàn tồn kho (tùy chọn)
        foreach ($order->orderItems as $item) {
            $item->product->increment('stock_quantity', $item->quantity);
        }
        return redirect()->route('admin.orders')->with('success', 'Đã hủy đơn hàng!');
    }

    // ==================== QUẢN LÝ NGƯỜI DÙNG ====================
    public function users(Request $request)
    {
        $query = User::with('roles');
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%');
        }
        $users = $query->orderBy('created_at', 'desc')->paginate(10);
        $roles = Role::all(); // lấy danh sách role
        return view('admin.users.index', compact('users'));
    }

    public function createUser()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole($validated['role']);

        return redirect()->route('admin.users')->with('success', 'Tạo người dùng thành công!');
    }

    public function editUser($id)
    {
        $user = User::with('roles')->findOrFail($id);
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:8|confirmed',
            'role' => 'required|exists:roles,name',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'] ? Hash::make($validated['password']) : $user->password,
        ]);

        $user->syncRoles([$validated['role']]);

        return redirect()->route('admin.users')->with('success', 'Cập nhật người dùng thành công!');
    }

    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users')->with('success', 'Xóa người dùng thành công!');
    }

    public function updateUserRole(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'role' => 'required|exists:roles,name',
        ]);

        $user->syncRoles([$validated['role']]);

        return response()->json(['message' => 'Cập nhật vai trò thành công!']);
    }

    // ==================== QUẢN LÝ BÁO CÁO ====================
    public function reports()
    {
        $revenue = Order::where('payment_status', 'paid')->sum('total_amount');
        $topProducts = Product::withCount('orderItems')->orderBy('order_items_count', 'desc')->take(5)->get();
        $topCustomers = Order::where('payment_status', 'paid')
            ->selectRaw('customer_id, SUM(total_amount) as total_spent, COUNT(*) as order_count')
            ->groupBy('customer_id')
            ->orderBy('total_spent', 'desc')
            ->take(5)
            ->get()
            ->map(function ($order) {
                $customer = $order->customer;
                return (object) [
                    'name' => $customer ? $customer->name : 'Khách vãng lai',
                    'email' => $customer ? $customer->email : 'N/A',
                    'total_spent' => $order->total_spent,
                    'order_count' => $order->order_count,
                ];
            });
        return view('admin.reports.index', compact('revenue', 'topProducts', 'topCustomers'));
    }
        public function revenueReport()
    {
        $revenueByMonth = Order::where('payment_status', 'paid')
            ->selectRaw('MONTH(created_at) as month, SUM(total_amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month')
            ->all();
        return view('admin.reports.revenue', compact('revenueByMonth'));
    }

    public function productReport()
    {
        $topProducts = Product::withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->paginate(10);
        return view('admin.reports.products', compact('topProducts'));
    }

    public function customerReport()
    {
        $topCustomers = Order::where('payment_status', 'paid')
            ->selectRaw('customer_id, SUM(total_amount) as total_spent, COUNT(*) as order_count')
            ->groupBy('customer_id')
            ->orderBy('total_spent', 'desc')
            ->take(10)
            ->get()
            ->map(function ($order) {
                $customer = $order->customer;
                return (object) [
                    'name' => $customer ? $customer->name : 'Khách vãng lai',
                    'email' => $customer ? $customer->email : 'N/A',
                    'total_spent' => $order->total_spent,
                    'order_count' => $order->order_count,
                ];
            });
        return view('admin.reports.customers', compact('topCustomers'));
    }

    public function exportRevenue()
    {
        $revenueByMonth = Order::where('payment_status', 'paid')
            ->selectRaw('MONTH(created_at) as month, SUM(total_amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'Tháng' => date('F', mktime(0, 0, 0, $item->month, 1)),
                    'Doanh thu' => number_format($item->total, 0, ',', '.') . ' ₫',
                ];
            });

        $export = $revenueByMonth->prepend(['Tháng', 'Doanh thu']);

        return response()->streamDownload(function () use ($export) {
            $handle = fopen('php://output', 'w');
            foreach ($export as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, 'revenue_report_' . date('Ymd_His') . '.csv', [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="revenue_report_' . date('Ymd_His') . '.csv"',
        ]);
    }

    public function exportProducts()
    {
        $topProducts = Product::withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->take(10)
            ->get()
            ->map(function ($product) {
                return [
                    'Tên sản phẩm' => $product->name,
                    'Số lượng bán' => $product->order_items_count,
                    'SKU' => $product->sku,
                ];
            });

        $export = $topProducts->prepend(['Tên sản phẩm', 'Số lượng bán', 'SKU']);

        return response()->streamDownload(function () use ($export) {
            $handle = fopen('php://output', 'w');
            foreach ($export as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, 'product_report_' . date('Ymd_His') . '.csv', [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="product_report_' . date('Ymd_His') . '.csv"',
        ]);
    }

    // ==================== CÀI ĐẶT HỆ THỐNG ====================
    public function settings()
    {
        $settings = Setting::first() ?? new Setting(); // Giả sử có model Setting
        return view('admin.settings.index', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
        ]);

        $setting = Setting::first() ?? new Setting();
        $setting->updateOrCreate(['id' => $setting->id ?? null], $validated);

        return redirect()->route('settings')->with('success', 'Cài đặt đã được cập nhật!');
    }


}
