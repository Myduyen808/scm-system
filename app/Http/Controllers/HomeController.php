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
        $user = auth()->user();
        $featuredProducts = null;
        $orderCount = Order::count();
        $productCount = Product::count();
        $userCount = User::count();
        $totalStock = Product::withSum('inventory', 'stock')->get()->sum('inventory_sum_stock');

        // Chỉ hiển thị sản phẩm approved và active cho Customer hoặc khi chưa đăng nhập
        if (!$user || $user->hasRole('customer')) {
            $featuredProducts = Product::approved()
                ->where('is_active', true) // Thêm kiểm tra is_active
                ->with('inventory')
                ->whereHas('inventory', function ($query) {
                    $query->where('stock', '>', 0);
                })
                ->orderBy('created_at', 'desc')
                ->limit(6)
                ->get();
        }

        if ($user) {
            $role = $user->roles->first()->name ?? null;
            switch ($role) {
                case 'admin':
                    return redirect()->route('admin.dashboard');
                case 'employee':
                    return redirect()->route('employee.dashboard');
                case 'customer':
                    return redirect()->route('customer.home');
                case 'supplier':
                    return redirect()->route('supplier.dashboard');
            }
        }

        return view('home', compact('featuredProducts', 'orderCount', 'productCount', 'userCount', 'totalStock'));
    }
}
