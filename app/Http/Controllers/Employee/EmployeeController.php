<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\Review;
use App\Models\SupportTicket;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function dashboard()
    {
        $totalProducts = Product::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $openTickets = SupportTicket::where('status', 'open')->count();
        $activePromotions = Promotion::where('is_active', true)->count();
        $orderStats = [
            'pending' => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'completed' => Order::where('status', 'completed')->count(),
        ];
        $reviews = Review::latest()->take(5)->get(); // Lấy 5 đánh giá mới nhất

        return view('employee.dashboard', compact('totalProducts', 'pendingOrders', 'openTickets', 'activePromotions', 'orderStats', 'reviews'));
    }

    public function reviews()
    {
        $reviews = Review::latest()->paginate(10);
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

    public function inventory()
    {
        // Logic cho quản lý kho
        return view('employee.inventory');
    }

    public function orders()
    {
        // Logic cho quản lý đơn hàng
        return view('employee.orders');
    }

    public function support()
    {
        // Logic cho hỗ trợ khách hàng
        return view('employee.support');
    }
}
