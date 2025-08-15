<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\Promotion;
use App\Models\SupportTicket;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    public function dashboard()
    {
        $totalProducts = Product::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $openTickets = SupportTicket::where('status', 'open')->count();
        return view('employee.dashboard', compact('totalProducts', 'pendingOrders', 'openTickets'));
    }

    // Quản lý kho
    public function inventory(Request $request)
    {
        if (!Auth::user()->can('manage inventory')) {
            abort(403);
        }
        $query = Product::query();
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
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
            'regular_price' => 'required|numeric',
            'stock_quantity' => 'required|integer|min:0',
            'sku' => 'required|string|unique:products',
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
        Product::findOrFail($productId)->delete();
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

    public function updateOrderStatus(Request $request, $orderId)
    {
        if (!Auth::user()->can('manage orders')) {
            abort(403);
        }
        $order = Order::findOrFail($orderId);
        $validated = $request->validate(['status' => 'required|in:pending,processing,shipped,delivered,cancelled']);
        $order->update($validated);
        return redirect()->back()->with('success', 'Cập nhật trạng thái đơn hàng thành công!');
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
        $validated = $request->validate(['reply' => 'required|string']);
        // Logic lưu reply (cần bảng chat hoặc cột reply trong SupportTicket)
        $ticket = SupportTicket::findOrFail($ticketId);
        $ticket->update(['status' => 'closed', 'reply' => $validated['reply']]); // Ví dụ
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
}
