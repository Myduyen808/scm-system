<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\Promotion;
use App\Models\Ticket;
use App\Models\RequestModel; // Thêm dòng này
use App\Models\Review; // Thêm dòng này
use App\Models\SupportTicket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\NotificationService; // Thêm service thông báo


class EmployeeController extends Controller
{
    protected $notificationService;
    public function __construct(NotificationService $notificationService)
    {
        $this->middleware('auth');
        $this->middleware('role:employee');
        $this->notificationService = $notificationService; // Tiêm service
    }

    public function dashboard()
    {
        $totalProducts = Product::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $openTickets = SupportTicket::where('status', 'open')->count();
        $activePromotions = Promotion::where('is_active', true)
                                    ->whereDate('start_date', '<=', now())
                                    ->whereDate('end_date', '>=', now())
                                    ->count();
        $orderStats = [
            'pending' => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
        ];
        $approvedProducts = Product::where('is_approved', true)
            ->with('inventory')
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();
        $reviews = Review::with(['product', 'user'])
            ->latest()
            ->limit(5)
            ->get();

        // Doanh thu theo tháng (12 tháng)
        $monthlyRevenues = Order::where('status', 'delivered')
            ->selectRaw('MONTH(created_at) as month, SUM(total_amount) as revenue')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('revenue', 'month')
            ->all();
        $labels = ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'];
        $revenueData = array_map(function($month) use ($monthlyRevenues) {
            return $monthlyRevenues[$month] ?? 0;
        }, range(1, 12));

        return view('employee.dashboard', compact(
            'totalProducts',
            'pendingOrders',
            'openTickets',
            'activePromotions',
            'orderStats',
            'approvedProducts',
            'reviews',
            'labels',
            'revenueData'
        ));
    }

    // Quản lý kho (Inventory)
    public function inventory(Request $request)
    {
        if (!Auth::user()->can('manage inventory')) {
            abort(403, 'Bạn không có quyền truy cập.');
        }
        $query = Product::where('supplier_id', '!=', null); // Chỉ lấy sản phẩm từ Supplier
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('sku', 'like', '%' . $request->search . '%');
        }
        $products = $query->with('supplier')->paginate(10);
        return view('employee.inventory.index', compact('products'));
    }

    public function updateInventory(Request $request, $productId)
    {
        if (!Auth::user()->can('manage inventory')) {
            abort(403);
        }
        $product = Product::findOrFail($productId);
        $validated = $request->validate(['stock_quantity' => 'required|integer|min:0']);
        $product->update($validated);
        return redirect()->back()->with('success', 'Cập nhật tồn kho thành công!');
    }

    public function createInventory()
    {
        if (!Auth::user()->can('manage inventory')) {
            abort(403);
        }
        return view('employee.inventory.create');
    }

    public function storeInventory(Request $request)
    {
        if (!Auth::user()->can('manage inventory')) {
            abort(403);
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'regular_price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'sku' => 'required|string|unique:products',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048', // Hình ảnh tối đa 2MB
        ]);
        Product::create($validated);
        return redirect()->route('employee.inventory')->with('success', 'Thêm sản phẩm thành công!');
    }

    public function showProduct($productId)
    {
        if (!Auth::user()->can('manage inventory')) {
            abort(403);
        }
        $product = Product::findOrFail($productId);
        return view('employee.inventory.show', compact('product'));
    }

    public function destroyInventory($productId)
    {
        if (!Auth::user()->can('manage inventory')) {
            abort(403);
        }
        $product = Product::findOrFail($productId);
        $product->delete();
        return redirect()->route('employee.inventory')->with('success', 'Xóa sản phẩm thành công!');
    }

    // Quản lý đơn hàng
    public function orders(Request $request)
    {
        if (!Auth::user()->can('manage orders')) {
            abort(403);
        }
        $query = Order::query();
        if ($request->search) {
            $query->where('order_number', 'like', '%' . $request->search . '%');
        }
        $orders = $query->paginate(10);
        return view('employee.orders.index', compact('orders'));
    }

    public function showOrder($orderId)
    {
        if (!Auth::user()->can('manage orders')) {
            abort(403);
        }
        $order = Order::with('orderItems')->findOrFail($orderId);
        return view('employee.orders.show', compact('order'));
    }

    public function cancelOrder($orderId)
    {
        if (!Auth::user()->can('manage orders')) {
            abort(403);
        }
        $order = Order::findOrFail($orderId);
        $order->update(['status' => 'cancelled']);
        return redirect()->back()->with('success', 'Hủy đơn hàng thành công!');
    }

    // Quản lý khuyến mãi
    public function promotions()
    {
        if (!Auth::user()->can('manage promotions')) {
            abort(403);
        }
        $promotions = Promotion::paginate(10);
        return view('employee.promotions.index', compact('promotions'));
    }

    public function createPromotion()
    {
        if (!Auth::user()->can('manage promotions')) {
            abort(403);
        }
        return view('employee.promotions.create');
    }

    public function storePromotion(Request $request)
    {
        if (!Auth::user()->can('manage promotions')) {
            abort(403);
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'discount' => 'required|numeric|min:0|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);
        Promotion::create($validated);
        return redirect()->route('employee.promotions')->with('success', 'Thêm khuyến mãi thành công!');
    }

    public function editPromotion($promotionId)
    {
        if (!Auth::user()->can('manage promotions')) {
            abort(403);
        }
        $promotion = Promotion::findOrFail($promotionId);
        return view('employee.promotions.edit', compact('promotion'));
    }

    public function updatePromotion(Request $request, $promotionId)
    {
        if (!Auth::user()->can('manage promotions')) {
            abort(403);
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'discount' => 'required|numeric|min:0|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);
        $promotion = Promotion::findOrFail($promotionId);
        $promotion->update($validated);
        return redirect()->route('employee.promotions')->with('success', 'Cập nhật khuyến mãi thành công!');
    }

    public function destroyPromotion($promotionId)
    {
        if (!Auth::user()->can('manage promotions')) {
            abort(403);
        }
        Promotion::findOrFail($promotionId)->delete();
        return redirect()->route('employee.promotions')->with('success', 'Xóa khuyến mãi thành công!');
    }

    // Phản hồi yêu cầu (xử lý nhập hàng từ Supplier)
    public function requests()
    {
        if (!Auth::user()->can('manage inventory')) {
            abort(403, 'Bạn không có quyền truy cập.');
        }
        $requests = RequestModel::where('supplier_id', Auth::id())->with(['product', 'supplier'])->paginate(10);
        return view('employee.requests.index', compact('requests'));
    }

    public function processRequest(Request $request, $id)
    {
        if (!Auth::user()->can('manage inventory')) {
            abort(403, 'Bạn không có quyền truy cập.');
        }
        $requestModel = RequestModel::findOrFail($id);
        $validated = $request->validate([
            'status' => 'required|in:accepted,rejected',
            'note' => 'nullable|string|max:255',
        ]);
        $requestModel->update([
            'status' => $validated['status'],
            'note_from_supplier' => $validated['note'] ?? $requestModel->note_from_supplier,
            'updated_at' => now(),
        ]);
        return redirect()->route('employee.requests')->with('success', 'Yêu cầu đã được xử lý. Trạng thái: ' . ucfirst($validated['status']));
    }

    // Hỗ trợ khách hàng
    public function support()
    {
        if (!Auth::user()->can('support customer')) {
            abort(403);
        }
        $tickets = SupportTicket::paginate(10);
        return view('employee.support.index', compact('tickets'));
    }

    public function replySupportTicket($id)
    {
        $ticket = Ticket::findOrFail($id);
        if ($ticket->assigned_to !== Auth::id() || !in_array($ticket->status, ['assigned', 'replied'])) {
            return redirect()->route('employee.employeeSupport')->with('error', 'Bạn không được phép trả lời ticket này!');
        }
        return view('employee.support.reply', compact('ticket'));
    }

    public function storeSupportReply(Request $request, $ticketId)
    {
        if (!Auth::user()->can('support customer')) {
            abort(403);
        }
        $validated = $request->validate(['reply' => 'required|string|max:2000']);
        $ticket = SupportTicket::findOrFail($ticketId);
        $ticket->update([
            'reply' => $validated['reply'],
            'status' => 'closed',
            'employee_id' => Auth::id(),
        ]);
        return redirect()->route('employee.support')->with('success', 'Phản hồi thành công!');
    }

    // Báo cáo doanh thu
    public function reports(Request $request)
    {
        if (!Auth::user()->can('view reports')) {
            abort(403);
        }
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        $revenue = Order::where('payment_status', 'paid')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->sum('total_amount');
        $topProducts = Product::withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->take(5)
            ->get();
        return view('employee.reports.index', compact('revenue', 'topProducts', 'month', 'year'));
    }

    public function index()
    {
        $tickets = SupportTicket::where('assigned_to', auth()->id())->get();
        return view('employee.support.index', compact('tickets'));
    }

    public function updateOrderStatus(Request $request, $order)
    {
        $order = Order::findOrFail($order);
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'tracking_number' => 'nullable|string',
            'shipping_note' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $order->update([
                'status' => $request->status,
                'tracking_number' => $request->tracking_number,
                'shipping_note' => $request->shipping_note,
            ]);

            if ($request->status === 'delivered') {
                $order->delivered_at = now();
                $order->save();

                // Giảm tồn kho từ bảng inventories
                foreach ($order->orderItems as $item) {
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

            DB::commit();
            return redirect()->route('employee.orders')->with('success', 'Cập nhật trạng thái đơn hàng thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Lỗi cập nhật trạng thái đơn hàng: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi khi cập nhật trạng thái: ' . $e->getMessage());
        }
    }

    public function reviews()
    {
        $reviews = Review::with(['product', 'user'])->latest()->paginate(10);
        return view('employee.reviews.index', compact('reviews'));
    }

    public function showReview(Review $review)
    {
        return view('employee.reviews.show', compact('review'));
    }

    public function destroyReview(Review $review)
    {
        $review->delete();
        return redirect()->route('employee.reviews')->with('success', 'Đánh giá đã được xóa thành công.');
    }

    // Trong EmployeeController.php
    public function showRequest($id)
    {
        if (!Auth::user()->can('manage inventory')) {
            abort(403, 'Bạn không có quyền truy cập.');
        }
        $request = RequestModel::with(['product', 'supplier'])->findOrFail($id);
        return view('employee.requests.show', compact('request'));
    }

    public function sendFeedback(Request $request, $id)
    {
        if (!Auth::user()->can('manage inventory')) {
            abort(403, 'Bạn không có quyền truy cập.');
        }
        $validated = $request->validate([
            'feedback' => 'required|string|max:1000',
            'status' => 'required|in:accepted,rejected',
        ]);
        $requestModel = RequestModel::findOrFail($id);
        $requestModel->update([
            'status' => $validated['status'],
            'note_from_supplier' => $validated['feedback'], // Thống nhất dùng note_from_supplier
            'updated_at' => now(),
        ]);
        // Thông báo cho nhân viên
        return redirect()->route('employee.requests')->with('success', 'Phản hồi đã được gửi thành công! Trạng thái yêu cầu đã cập nhật.');
    }

public function showStockRequestForm()
    {
        if (!Auth::user()->can('manage inventory')) {
            abort(403);
        }
        $lowStockProducts = Product::where('supplier_id', '!=', null)
            ->where('stock_quantity', '<', 10)
            ->with('supplier')
            ->get();
        $suppliers = \App\Models\User::role('supplier')->get(); // Lấy danh sách supplier
        return view('employee.dashboard-stock-request', compact('lowStockProducts', 'suppliers'));
    }

    public function sendStockRequest(Request $request)
    {
        if (!Auth::user()->can('manage inventory')) {
            abort(403);
        }

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'supplier_id' => 'required|exists:users,id',
            'note' => 'nullable|string|max:500',
        ]);

        $requestData = [
            'supplier_id' => $validated['supplier_id'],
            'employee_id' => Auth::id(),
            'product_id' => $validated['product_id'],
            'quantity' => $validated['quantity'],
            'description' => 'Yêu cầu nhập hàng',
            'employee_note' => $validated['note'] ?? '',
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
            'request_number' => 'REQ-' . str_pad(RequestModel::max('id') + 1, 3, '0', STR_PAD_LEFT),
        ];

        try {
            \DB::beginTransaction();
            $requestModel = RequestModel::create($requestData);
            \DB::commit();
            return redirect()->route('employee.requests')->with('success', 'Yêu cầu nhập hàng đã được gửi thành công!')->with('request_id', $requestModel->id);
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error creating request: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi gửi yêu cầu: ' . $e->getMessage())->withInput();
        }
    }

    public function employeeSupport()
    {
        $tickets = Ticket::where('assigned_to', Auth::id())
            ->where('status', 'assigned')
            ->paginate(10);
        return view('employee.support.index', compact('tickets'));
    }

    public function showTicket($id)
    {
        $ticket = Ticket::findOrFail($id);
        if ($ticket->assigned_to !== Auth::id()) {
            return redirect()->route('employee.employeeSupport')->with('error', 'Bạn không được phép xem ticket này!');
        }
        return view('employee.support.reply', compact('ticket')); // tên view đúng
    }

public function replyTicket(Request $request, $id)
{
    $ticket = Ticket::findOrFail($id);
    if ($ticket->assigned_to !== Auth::id() || !in_array($ticket->status, ['assigned', 'replied'])) {
        return redirect()->route('employee.employeeSupport')->with('error', 'Bạn không được phép trả lời ticket này!');
    }

    $validated = $request->validate(['message' => 'required|string|max:1000']);

    $ticket->replies()->create([
        'user_id' => Auth::id(),
        'message' => $validated['message'],
    ]);

    $ticket->update(['status' => 'replied']);

    return redirect()->route('employee.employeeSupport')->with('success', 'Phản hồi đã được gửi!');
}


    public function respondToRequest(Request $request, $id)
    {
        if (!Auth::user()->can('manage supplier requests')) {
            abort(403);
        }

        $requestModel = RequestModel::findOrFail($id);
        if ($requestModel->supplier_id != Auth::id()) {
            abort(403, 'Bạn không được phép phản hồi yêu cầu này.');
        }

        $validated = $request->validate([
            'note_from_supplier' => 'required|string|max:1000',
            'status' => 'required|in:accepted,rejected',
        ]);

        $requestModel->update([
            'note_from_supplier' => $validated['note_from_supplier'],
            'status' => $validated['status'],
            'updated_at' => now(),
        ]);

        // Gửi thông báo cho nhân viên
        if ($requestModel->employee_id) {
            $this->notificationService->notifyEmployeeRequestResponse($requestModel, $requestModel->employee_id);
        }

        return redirect()->route('supplier.requests')->with('success', 'Phản hồi đã được gửi thành công!');
    }
}
