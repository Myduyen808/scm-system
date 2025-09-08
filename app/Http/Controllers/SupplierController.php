<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\RequestModel; // tên class chính xác
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\Inventory;
use App\Services\NotificationService;

class SupplierController extends Controller
{
    protected $notificationService;
    public function __construct(NotificationService $notificationService)
    {
        $this->middleware('auth');
        $this->middleware('role:supplier');
        $this->notificationService = $notificationService;
    }

    // Dashboard

public function dashboard()
{
    $supplierId = Auth::id();

    // Thống kê cơ bản
    $totalProducts = Product::where('supplier_id', $supplierId)->count();
    $pendingOrders = Order::where('status', 'pending')
        ->whereHas('orderItems.product', function ($query) use ($supplierId) {
            $query->where('supplier_id', $supplierId);
        })->count();
    $monthlyRevenue = Order::where('status', 'delivered')
        ->whereHas('orderItems.product', function ($query) use ($supplierId) {
            $query->where('supplier_id', $supplierId);
        })
        ->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
        ->sum('total_amount') ?: 0;

    // Lấy doanh thu theo tháng cho 12 tháng gần nhất
    $monthlyRevenues = Order::where('status', 'delivered')
        ->whereHas('orderItems.product', function ($query) use ($supplierId) {
            $query->where('supplier_id', $supplierId);
        })
        ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(total_amount) as revenue')
        ->groupBy('year', 'month')
        ->get()
        ->keyBy(function ($item) {
            return $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT);
        })
        ->map->revenue
        ->all();

    // Tạo labels và data cho 12 tháng gần nhất
    $labels = [];
    $data = [];
    $currentMonth = Carbon::now()->month;
    $currentYear = Carbon::now()->year;

    for ($i = 11; $i >= 0; $i--) {
        $month = Carbon::now()->subMonths($i);
        $monthKey = $month->year . '-' . str_pad($month->month, 2, '0', STR_PAD_LEFT);
        $labels[] = 'Tháng ' . $month->month . ' ' . $month->year;
        $data[] = $monthlyRevenues[$monthKey] ?? 0;
    }

    $pendingApprovalCount = Product::where('supplier_id', $supplierId)->where('is_approved', false)->count();

    return view('supplier.dashboard', compact(
        'totalProducts',
        'pendingOrders',
        'monthlyRevenue',
        'labels',
        'data',
        'pendingApprovalCount'
    ));
}
    // Danh sách sản phẩm
    public function products(Request $request)
    {
        $query = Product::where('supplier_id', Auth::id())
            ->with(['orderItems' => function ($query) {
                $query->whereHas('order', function ($q) {
                    $q->where('status', 'delivered'); // Đảm bảo dùng 'delivered'
                })->with('order'); // Tải thêm thông tin order nếu cần
            }])
            ->with('inventory');

        if ($request->input('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->input('search') . '%')
                ->orWhere('sku', 'like', '%' . $request->input('search') . '%');
            });
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(10);

        // Debug log để kiểm tra dữ liệu
        \Log::info('Products with OrderItems: ', $products->toArray());

        return view('supplier.products.index', compact('products'));
    }

    // Tạo sản phẩm mới
    public function createProduct()
    {
        return view('supplier.products.create');
    }

// Sửa method storeProduct
    public function storeProduct(Request $request)
    {
        $regularPrice = (float) $request->input('regular_price', 0);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'regular_price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|max:' . $regularPrice,
            'sale_percent' => 'nullable|numeric|min:0|max:100',
            'sku' => 'required|string|unique:products',
            'stock_quantity' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        $validated['supplier_id'] = Auth::id();

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        // Xử lý sale_price theo VND hoặc %
        $salePriceInput = $request->filled('sale_price') ? (float) $request->input('sale_price') : null;
        $salePercent = (float) $request->input('sale_percent', 0);

        if ($salePercent > 0) {
            $salePrice = $regularPrice * (1 - $salePercent / 100);
        } else {
            $salePrice = $salePriceInput;
        }

        if ($salePrice !== null && $salePrice >= $regularPrice) {
            return back()->with('error', 'Giá sale phải nhỏ hơn giá thường!')->withInput();
        }

        $validated['sale_price'] = $salePrice;
        $validated['sale_percent'] = $salePercent;
        $validated['current_price'] = $salePrice && $salePrice > 0 ? $salePrice : $regularPrice;

        \DB::beginTransaction();
        try {
            $product = Product::create($validated);

            Inventory::create([
                'product_id' => $product->id,
                'stock' => $validated['stock_quantity'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Gửi thông báo cho tất cả admin
        $admins = \App\Models\User::role('admin')->get();
        foreach ($admins as $admin) {
            $this->notificationService->createNotification(
                $admin->id,
                'new_product_pending',
                'Sản phẩm mới chờ phê duyệt',
                "Nhà cung cấp '{$product->supplier->name}' đã thêm sản phẩm '{$product->name}' cần phê duyệt. Xem chi tiết tại: " . route('admin.pending.products'),
                ['product_id' => $product->id],
                $product->id,
                'Product'
            );
        }

            \DB::commit();
            return redirect()->route('supplier.products')->with('success', 'Sản phẩm đã được thêm thành công!');
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra khi thêm sản phẩm: ' . $e->getMessage())->withInput();
        }
    }

// Sửa method updateProduct
    public function updateProduct(Request $request, $id)
    {
        $product = Product::where('supplier_id', Auth::id())->findOrFail($id);
        $regularPrice = (float) $request->input('regular_price', 0);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'regular_price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|max:' . $regularPrice,
            'sale_percent' => 'nullable|numeric|min:0|max:100',
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

        // Xử lý sale_price theo VND hoặc %
        $salePriceInput = $request->filled('sale_price') ? (float) $request->input('sale_price') : null;
        $salePercent = (float) $request->input('sale_percent', 0);

        if ($salePercent > 0) {
            $salePrice = $regularPrice * (1 - $salePercent / 100);
        } else {
            $salePrice = $salePriceInput;
        }

        if ($salePrice !== null && $salePrice >= $regularPrice) {
            return back()->with('error', 'Giá sale phải nhỏ hơn giá thường!')->withInput();
        }

        $validated['sale_price'] = $salePrice;
        $validated['sale_percent'] = $salePercent;
        $validated['current_price'] = $salePrice && $salePrice > 0 ? $salePrice : $regularPrice;

        \DB::beginTransaction();
        try {
            $product->update($validated);

            $inventory = Inventory::firstOrCreate(
                ['product_id' => $product->id],
                ['stock' => $validated['stock_quantity'], 'created_at' => now(), 'updated_at' => now()]
            );
            if (!$inventory->wasRecentlyCreated) {
                $inventory->update(['stock' => $validated['stock_quantity'], 'updated_at' => now()]);
            }

            \DB::commit();
            return redirect()->route('supplier.products')->with('success', 'Sản phẩm đã được cập nhật thành công!');
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra khi cập nhật sản phẩm: ' . $e->getMessage())->withInput();
        }
    }


    // Sửa sản phẩm
    public function editProduct($id)
    {
        $product = Product::where('supplier_id', Auth::id())->findOrFail($id);
        return view('supplier.products.edit', compact('product'));
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

        $orders = $query->with('customer')
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);

        return view('supplier.orders.index', compact('orders'));
    }

    // Xem chi tiết đơn hàng
    public function showOrder($id)
    {
        $order = Order::with('orderItems.product', 'customer')
                    ->findOrFail($id);
        if (!$order->orderItems->where('product.supplier_id', Auth::id())->count()) {
            abort(403, 'Bạn không có quyền xem đơn hàng này.');
        }
        return view('supplier.orders.show', compact('order'));
    }

    // Danh sách yêu cầu nhập hàng
    public function requests(Request $request)
    {
        $query = RequestModel::where('supplier_id', Auth::id());
        if ($search = $request->input('search')) {
            $query->where('request_number', 'like', "%{$search}%");
        }
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        $requests = $query->with(['product', 'supplier'])->orderBy('created_at', 'desc')->paginate(10);
        return view('supplier.requests.index', compact('requests'));
    }


    public function productReport()
    {
        $products = Product::where('supplier_id', Auth::id())
            ->with(['orderItems' => function ($query) {
                $query->whereHas('order', function ($q) {
                    $q->where('status', 'delivered'); // Giữ nguyên 'delivered'
                })->with('order'); // Thêm with('order') để đồng bộ với products method
            }])
            ->get();

        $reports = $products->map(function ($product) {
            $soldQuantity = $product->orderItems->sum('quantity') ?? 0;
            $revenue = $product->orderItems->sum(function ($item) {
                return ($item->price ?? 0) * ($item->quantity ?? 0);
            }) ?? 0;
            return [
                'name' => $product->name,
                'sold_quantity' => $soldQuantity,
                'revenue' => $revenue,
                'image' => $product->image ?? null, // Giữ cột image
            ];
        });

        // Debug log để kiểm tra
        \Log::info('Product Report Data: ', $reports->toArray());

        return view('supplier.products.report', compact('reports'));
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        if (!$order->orderItems->where('product.supplier_id', Auth::id())->count()) {
            abort(403);
        }
        $validated = $request->validate(['status' => 'required|in:processing,completed,shipped,delivered']);

        DB::beginTransaction();
        try {
            $order->update($validated);

            if ($request->status === 'delivered') {
                // Giảm tồn kho từ bảng inventories
                foreach ($order->orderItems as $item) {
                    if ($item->product->supplier_id === Auth::id()) {
                        $inventory = $item->product->inventory;
                        if ($inventory) {
                            $newStock = $inventory->stock - $item->quantity;
                            if ($newStock < 0) {
                                throw new \Exception('Số lượng tồn kho không đủ cho sản phẩm ' . $item->product->name);
                            }
                            $inventory->update(['stock' => $newStock]);
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('supplier.orders')->with('success', 'Cập nhật trạng thái đơn hàng thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Lỗi cập nhật trạng thái đơn hàng: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi khi cập nhật trạng thái: ' . $e->getMessage());
        }
    }

    // Thông báo khi admin hoặc nhân viên phê duyệt sản phẩm
    public function checkApprovalNotifications()
    {
        $pendingProducts = Product::where('supplier_id', Auth::id())
            ->where('is_approved', false)
            ->where('updated_at', '>', Auth::user()->last_notification_check)
            ->get();
        if ($pendingProducts->count() > 0) {
            Auth::user()->update(['last_notification_check' => now()]);
            return redirect()->route('supplier.dashboard')->with('info', 'Có ' . $pendingProducts->count() . ' sản phẩm được cập nhật trạng thái.');
        }
        return redirect()->route('supplier.dashboard');
    }

    public function showRequest($id)
    {
        $request = RequestModel::where('supplier_id', Auth::id())->findOrFail($id);
        return view('supplier.requests.show', compact('request'));
    }

public function processRequest(Request $request, $id)
{
    $requestModel = RequestModel::where('supplier_id', Auth::id())->findOrFail($id);

    $validated = $request->validate([
        'status' => 'required|in:accepted,rejected',
        'note' => 'nullable|string|max:255'
    ]);

    \DB::beginTransaction();
    try {
        $note = $validated['note'] ?? null;
        $message = "Yêu cầu #{$requestModel->request_number} đã được {$validated['status']} với ghi chú: " . ($note ?? 'Không có');
        $requestModel->update([
            'status' => $validated['status'],
            'note_from_supplier' => $note,
            'updated_at' => now()
        ]);
        $requestModel->replies()->create(['user_id' => Auth::id(), 'message' => $message]);

        if ($requestModel->employee_id) {
            $this->notificationService->createNotification(
                $requestModel->employee_id,
                'request_supplier_reply',
                'Nhà cung cấp phản hồi yêu cầu',
                "Nhà cung cấp {$requestModel->supplier->name} đã {$validated['status']} yêu cầu #{$requestModel->request_number}: {$message}",
                ['request_id' => $requestModel->id],
                $requestModel->id,
                'RequestModel'
            );
        }

        \DB::commit();
        return redirect()->route('supplier.requests')->with('success', 'Xử lý yêu cầu thành công!');
    } catch (\Exception $e) {
        \DB::rollBack();
        \Log::error('Lỗi xử lý yêu cầu: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Lỗi chi tiết: ' . $e->getMessage());
    }
}

public function replyRequest(Request $request, $id)
{
    $requestModel = RequestModel::where('supplier_id', Auth::id())->findOrFail($id);
    if ($requestModel->status === 'closed') {
        return redirect()->back()->with('error', 'Yêu cầu đã đóng, không thể phản hồi!');
    }

    $validated = $request->validate(['message' => 'required|string|max:1000']);
    $requestModel->replies()->create(['user_id' => Auth::id(), 'message' => $validated['message']]);

    if ($requestModel->employee_id) {
        $this->notificationService->createNotification(
            $requestModel->employee_id,
            'request_supplier_reply',
            'Nhà cung cấp phản hồi yêu cầu',
            "Nhà cung cấp {$requestModel->supplier->name} đã phản hồi yêu cầu #{$requestModel->request_number}: {$validated['message']}",
            ['request_id' => $requestModel->id],
            $requestModel->id,
            'RequestModel'
        );
    }

    return redirect()->route('supplier.requests.show', $requestModel->id)->with('success', 'Phản hồi đã được gửi!');
}

}
