<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        // Lấy danh sách sản phẩm nổi bật
        $featuredProducts = Product::where('is_active', true)->limit(6)->get();

        $user = auth()->user();
        $orderCount = Order::count();
        $productCount = Product::count();
        $userCount = User::count();

        // Kiểm tra vai trò và chuyển hướng nếu đã đăng nhập
        if ($user) {
            if ($user->hasRole('admin')) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->hasRole('employee')) {
                return redirect()->route('employee.dashboard');
            } elseif ($user->hasRole('customer')) {
                return redirect()->route('customer.home');
            } elseif ($user->hasRole('supplier')) {
                return redirect()->route('supplier.dashboard');
            }
        }

        // Nếu chưa đăng nhập hoặc không có vai trò, hiển thị trang home
        return view('home', compact('featuredProducts','orderCount', 'productCount', 'userCount'));
    }
}
