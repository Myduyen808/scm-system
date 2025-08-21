<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\Promotion;
use App\Models\Review; // Thêm dòng này
use App\Models\SupportTicket;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:employee');
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
            'completed' => Order::where('status', 'completed')->count(),
        ];
        // Lấy danh sách sản phẩm đã phê duyệt gần đây (giới hạn 5)
        $approvedProducts = Product::where('is_approved', true)
            ->with('inventory')
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();
        // Lấy 5 đánh giá mới nhất
        $reviews = Review::with(['product', 'user'])
            ->latest()
            ->limit(5)
            ->get();

        return view('employee.dashboard', compact(
            'totalProducts',
            'pendingOrders',
            'openTickets',
            'activePromotions',
            'orderStats',
            'approvedProducts',
            'reviews'
        ));
    }

    // Quản lý kho (Inventory)
    public function inventory(Request $request)
    {
        if (!Auth::user()->can('manage inventory')) {
            abort(403, 'Bạn không có quyền truy cập.');
        }
        $query = Product::query();
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%');
        }
        $products = $query->paginate(10);
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
        if (!Auth::user()->can('support customer')) { // Sử dụng permission hiện có
            abort(403);
        }
        // Giả sử có model Request hoặc lấy từ bảng liên quan
        $requests = []; // Thay bằng logic thực tế
        return view('employee.requests.index', compact('requests'));
    }

    public function processRequest($requestId)
    {
        if (!Auth::user()->can('support customer')) {
            abort(403);
        }
        // Logic xử lý yêu cầu (ví dụ: cập nhật trạng thái, thông báo Supplier)
        return redirect()->back()->with('success', 'Xử lý yêu cầu thành công!');
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

    public function replySupportTicket($ticketId)
    {
        if (!Auth::user()->can('support customer')) {
            abort(403);
        }
        $ticket = SupportTicket::findOrFail($ticketId);
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

    public function replyTicket($ticketId, Request $request)
    {
        $request->validate(['message' => 'required|string']);
        $ticket = SupportTicket::findOrFail($ticketId);

        // Có thể tạo bảng replies hoặc dùng comment system
        $ticket->replies()->create([
            'user_id' => auth()->id(),
            'message' => $request->message
        ]);

        return back()->with('success', 'Trả lời ticket thành công!');
    }

        public function updateOrderStatus(Request $request, $order)
    {
        $order = Order::findOrFail($order);
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'tracking_number' => 'nullable|string',
            'shipping_note' => 'nullable|string',
        ]);

        $order->update([
            'status' => $request->status,
            'tracking_number' => $request->tracking_number,
            'shipping_note' => $request->shipping_note,
        ]);

        if ($request->status === 'delivered') {
            $order->delivered_at = now();
            $order->save();
        }

        return redirect()->route('employee.orders')->with('success', 'Cập nhật trạng thái đơn hàng thành công!');
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
}
