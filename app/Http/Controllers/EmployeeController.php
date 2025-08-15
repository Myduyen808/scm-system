<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    public function dashboard()
    {
        return view('employee.dashboard');
    }

    public function inventory()
    {
        if (!Auth::user()->can('manage inventory')) {
            abort(403);
        }
        $products = Product::all();
        return view('employee.inventory', compact('products'));
    }

    public function updateInventory(Request $request, $productId)
    {
        if (!Auth::user()->can('manage inventory')) {
            abort(403);
        }
        $product = Product::findOrFail($productId);
        $product->update(['stock_quantity' => $request->stock_quantity]);
        return redirect()->back()->with('success', 'Cập nhật tồn kho thành công!');
    }

    public function orders()
    {
        if (!Auth::user()->can('manage orders')) {
            abort(403);
        }
        $orders = Order::all();
        return view('employee.orders', compact('orders'));
    }

    public function updateOrderStatus(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);
        $order->update(['status' => $request->status]);
        return redirect()->back()->with('success', 'Cập nhật trạng thái đơn hàng thành công!');
    }

    public function promotions()
    {
        if (!Auth::user()->can('manage promotions')) {
            abort(403);
        }
        // Giả sử có model Promotion, thêm logic ở đây
        return view('employee.promotions');
    }

    public function support()
    {
        if (!Auth::user()->can('support customer')) {
            abort(403);
        }
        // Logic chat hoặc khiếu nại
        return view('employee.support');
    }

    public function reports()
    {
        if (!Auth::user()->can('view reports')) {
            abort(403);
        }
        $revenue = Order::where('payment_status', 'paid')->sum('total_amount');
        return view('employee.reports', compact('revenue'));
    }
}
