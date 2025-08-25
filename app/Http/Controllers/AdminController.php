<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\InventoryImport;
use App\Exports\OrdersExport;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use App\Models\Review; // Thêm import cho Review
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash; // vì bạn có dùng Hash::make()
use Spatie\Permission\Models\Role;   // thêm dòng này
use Illuminate\Support\Facades\Storage; // vì bạn có dùng Storage::exists()
use App\Models\Setting;
use App\Models\SupportTicket;
use App\Models\Promotion;
use Illuminate\Support\Facades\Response; // Thêm dòng này
use Illuminate\Support\Facades\Artisan;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Log;
// use App\Models\Supplier;


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
        $totalProducts = Product::count();
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $openTickets = SupportTicket::where('status', 'open')->count();
        $totalUsers = User::count();
        $totalRevenue = Order::where('status', 'delivered')->sum('total_amount'); // Đã sửa từ 'completed' thành 'delivered'
        $activePromotions = Promotion::where('is_active', true)
                                    ->whereDate('start_date', '<=', now())
                                    ->whereDate('end_date', '>=', now())
                                    ->count();

        $ticketToAssign = SupportTicket::where('status', 'open')->first();

        $orderStats = [
            'pending' => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'delivered' => Order::where('status', 'delivered')->count(), // Đã sửa từ 'completed' thành 'delivered'
        ];

        $monthlyRevenue = Order::selectRaw('MONTH(created_at) as month, SUM(total_amount) as revenue')
            ->where('status', 'delivered') // Đã sửa từ 'completed' thành 'delivered'
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('revenue', 'month');

        $revenueData = [];
        for ($i = 1; $i <= 12; $i++) {
            $revenueData[] = $monthlyRevenue[$i] ?? 0;
        }

        $todayOrders = Order::whereDate('created_at', today())->count();
        $lowStockProducts = Product::where('stock_quantity', '<', 10)->count();
        $topProducts = Product::withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->take(5)
            ->get();
        $recentOrders = Order::with('customer')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        $reviews = Review::with(['product', 'user'])->paginate(10); // Thêm truy vấn cho reviews

        return view('admin.dashboard', compact(
            'totalProducts', 'totalOrders', 'totalUsers', 'totalRevenue',
            'todayOrders', 'lowStockProducts', 'topProducts', 'recentOrders',
            'activePromotions', 'orderStats', 'openTickets', 'pendingOrders',
            'ticketToAssign', 'revenueData', 'reviews'
        ));
    }

        // ==================== QUẢN LÝ KHO ====================
    public function inventory(Request $request)
    {
        $query = Product::with('supplier');

        // Tìm kiếm
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%');
            });
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
        return response()->json(['message' => 'Cập nhật trạng thái thành công!']);
    }

    public function confirmPayment(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        // Kiểm tra nếu đã thanh toán thì không cần hành động
        if ($order->payment_status === 'paid') {
            return response()->json(['message' => 'Đơn hàng đã được thanh toán!'], 400);
        }

        // Cập nhật trạng thái thanh toán và chuyển status sang processing nếu từ pending
        $order->payment_status = 'paid';
        if ($order->status === 'pending') {
            $order->status = 'processing';
        }
        $order->save();

        return response()->json(['message' => 'Xác nhận thanh toán thành công!']);
    }

    public function cancelOrder($id)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => 'cancelled']);

        DB::beginTransaction();
        try {
            // Hoàn tồn kho nếu đơn hàng bị hủy
            foreach ($order->orderItems as $item) {
                $inventory = $item->product->inventory;
                if ($inventory) {
                    $inventory->increment('stock', $item->quantity);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Lỗi hoàn tồn kho khi hủy đơn hàng: ' . $e->getMessage());
        }

        return redirect()->route('admin.orders')->with('success', 'Đã hủy đơn hàng!');
    }

    // ==================== QUẢN LÝ NGƯỜI DÙNG ====================
    public function users(Request $request)
    {
        $query = User::with('roles'); // Tải quan hệ roles
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%');
        }
        $users = $query->orderBy('created_at', 'desc')->paginate(10);
        $roles = Role::all(); // Lấy tất cả vai trò để hiển thị trong dropdown

        return view('admin.users.index', compact('users', 'roles'));
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

        // Đảm bảo mảng đầy đủ 12 tháng, giá trị mặc định là 0 nếu không có dữ liệu
        $revenueByMonth = array_replace(array_fill(1, 12, 0), $revenueByMonth);

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
    public function promotions()
    {
        $promotions = Promotion::with(['products'])
                            ->whereDate('end_date', '>=', now())
                            ->orWhere('is_active', true)
                            ->orderBy('start_date', 'desc')
                            ->paginate(10);

        // Lấy tất cả sản phẩm chưa được áp dụng bất kỳ khuyến mãi nào
        $availableProducts = Product::whereDoesntHave('promotions')->get();

        return view('admin.promotions.index', compact('promotions', 'availableProducts'));
    }

    public function applyProduct(Request $request, $promotionId)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $promotion = Promotion::findOrFail($promotionId);

        // Kiểm tra nếu sản phẩm đã được áp dụng cho bất kỳ khuyến mãi nào
        $product = Product::findOrFail($request->product_id);
        if ($product->promotions()->exists()) {
            return response()->json(['message' => 'Sản phẩm đã được áp dụng cho một khuyến mãi khác!'], 400);
        }

        $promotion->products()->attach($request->product_id);

        return response()->json(['message' => 'Áp dụng sản phẩm thành công!']);
    }

    public function applyAllProducts(Request $request, $promotionId)
    {
        try {
            $promotion = Promotion::findOrFail($promotionId);

            // Lấy tất cả sản phẩm được phê duyệt và hoạt động
            $products = Product::where('is_approved', true)->where('is_active', true)->get();

            if ($products->isEmpty()) {
                return response()->json(['message' => 'Không có sản phẩm nào để áp dụng!'], 400);
            }

            // Gắn tất cả sản phẩm vào khuyến mãi
            $promotion->products()->syncWithoutDetaching($products->pluck('id')->toArray());

            Log::info('Applied promotion ' . $promotionId . ' to all products: ' . json_encode($products->pluck('id')));

            return response()->json(['message' => 'Áp dụng khuyến mãi cho tất cả sản phẩm thành công!']);
        } catch (\Exception $e) {
            Log::error('Error in applyAllProducts: ' . $e->getMessage());
            return response()->json(['message' => 'Có lỗi xảy ra khi áp dụng!'], 500);
        }
    }

    public function create()
    {
        return view('admin.promotions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'discount' => 'required|numeric|min:0|max:100',
            'is_active' => 'required|boolean',
        ]);

        Promotion::create([
            'name' => $request->name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'discount' => $request->discount,
            'is_active' => $request->is_active,
        ]);

        return redirect()->route('admin.promotions')->with('success', 'Khuyến mãi đã được thêm thành công.');
    }

    public function edit($id)
    {
        $promotion = Promotion::findOrFail($id);
        return view('admin.promotions.edit', compact('promotion'));
    }

    public function update(Request $request, $id)
    {
        $promotion = Promotion::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'discount' => 'required|numeric|min:0|max:100',
            'is_active' => 'required|boolean',
        ]);

        $promotion->update([
            'name' => $request->name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'discount' => $request->discount,
            'is_active' => $request->is_active,
        ]);

        return redirect()->route('admin.promotions')->with('success', 'Khuyến mãi đã được cập nhật thành công.');
    }

    public function destroy($id)
    {
        $promotion = Promotion::findOrFail($id);
        $promotion->delete();

        return redirect()->route('admin.promotions')->with('success', 'Khuyến mãi đã được xóa thành công.');
    }

    public function importInventory(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:xlsx,xls']);
        Excel::import(new InventoryImport, $request->file('file'));
        return redirect()->route('admin.inventory')->with('success', 'Nhập dữ liệu kho thành công!');
    }


    public function forecastInventory()
    {
        $products = Product::all();
        $forecasts = [];
        foreach ($products as $product) {
            // Logic đơn giản: Dự báo tồn kho tăng 10% dựa trên lịch sử (có thể mở rộng bằng AI sau)
            $forecast = $product->stock_quantity * 1.1; // Ví dụ
            $forecasts[] = ['product' => $product, 'forecast' => $forecast];
        }
        return view('admin.inventory.forecast', compact('forecasts'));
    }
    public function exportOrders()
    {
        return Excel::download(new OrdersExport, 'orders_' . now()->format('Ymd_His') . '.xlsx');
    }

    public function orderStats()
    {
        $totalRevenue = Order::where('status', 'delivered')->sum('total_amount'); // Doanh thu từ đơn giao thành công
        $monthlyRevenue = Order::selectRaw('MONTH(created_at) as month, SUM(total_amount) as revenue')
            ->where('status', 'delivered')
            ->groupBy('month')
            ->get();

        return view('admin.orders.stats', compact('totalRevenue', 'monthlyRevenue'));
    }
    public function export()
    {
        $orders = Order::all(); // Hoặc thêm điều kiện như where('payment_status', 'paid')

        $exportData = $orders->map(function ($order) {
            return [
                'ID' => $order->id,
                'Customer' => $order->customer ? $order->customer->name : 'Khách vãng lai',
                'Total Amount' => number_format($order->total_amount, 0, ',', '.') . ' ₫',
                'Payment Status' => $order->payment_status,
                'Created At' => $order->created_at->format('d/m/Y H:i'),
            ];
        });

        $headers = ['ID', 'Customer', 'Total Amount', 'Payment Status', 'Created At'];
        $exportData = $exportData->prepend($headers);

        $csvContent = $exportData->map(function ($row) {
            return implode(',', array_map(function ($value) {
                return str_replace(',', '', $value); // Loại bỏ dấu phẩy trong số để tránh lỗi CSV
            }, $row));
        })->implode("\n");

        return Response::make($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="orders_export_' . date('Ymd_His') . '.csv"',
        ]);
    }


    public function assignTicket($ticketId, Request $request)
    {
        $request->validate([
            'assigned_to' => 'required|exists:users,id'
        ]);

        $ticket = SupportTicket::findOrFail($ticketId);
        $ticket->update([
            'assigned_to' => $request->assigned_to,
            'status' => 'assigned' // Thêm trạng thái để đánh dấu ticket đã được phân công
        ]);

        return redirect()->route('admin.tickets')->with('success', 'Phân công ticket thành công!');
    }
    public function tickets()
    {
        $tickets = SupportTicket::with('user', 'assignedTo')->get();
        return view('admin.tickets.index', compact('tickets'));
    }

    public function backupDatabase()
    {
        Artisan::call('backup:run');
        return redirect()->back()->with('success', 'Sao lưu thành công!');
    }
    public function activityLogs()
    {
        $logs = Activity::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.logs', compact('logs'));
    }


}
