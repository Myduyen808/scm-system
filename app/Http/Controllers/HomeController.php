<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $featuredProducts = null;
        $orderCount = Order::count();
        $productCount = Product::count();
        $userCount = User::count();
        $totalStock = Product::withSum('inventory', 'stock')->get()->sum('inventory_sum_stock');

        // Số lượng sản phẩm sale (giảm giá)
        $saleProductCount = Product::whereNotNull('sale_price')
            ->where('sale_price', '<', \DB::raw('regular_price'))
            ->count();

        // Chỉ hiển thị sản phẩm approved và active cho Customer hoặc khi chưa đăng nhập
        if (!$user || $user->hasRole('customer')) {
            $featuredProducts = Product::approved()
                ->where('is_active', true)
                ->with('inventory')
                ->whereHas('inventory', function ($query) {
                    $query->where('stock', '>', 0);
                })
                ->orderByDesc(
                    \DB::raw('(CASE
                        WHEN sale_price IS NOT NULL AND sale_price < regular_price THEN (regular_price - sale_price) / regular_price * 100
                        ELSE 0
                    END) + (SELECT COALESCE(SUM(quantity), 0) FROM order_items WHERE order_items.product_id = products.id)')
                )
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

        return view('home', compact(
            'featuredProducts',
            'orderCount',
            'productCount',
            'userCount',
            'totalStock',
            'saleProductCount'
        ));
    }
}
