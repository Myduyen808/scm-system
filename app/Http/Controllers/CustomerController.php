<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function home()
    {
        $products = Product::where('is_active', true)->take(10)->get();
        return view('customer.home', compact('products'));
    }

    public function products()
    {
        if (!Auth::user()->can('view products')) {
            abort(403);
        }
        $products = Product::where('is_active', true)->paginate(12);
        return view('customer.products', compact('products'));
    }

    public function cart()
    {
        $cartItems = Auth::user()->cartItems()->with('product')->get();
        $total = $cartItems->sum('subtotal');
        return view('customer.cart', compact('cartItems', 'total'));
    }

    public function addToCart(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);
        Cart::create([
            'user_id' => Auth::id(),
            'product_id' => $productId,
            'quantity' => $request->quantity ?? 1,
        ]);
        return redirect()->back()->with('success', 'Đã thêm vào giỏ hàng!');
    }

    public function orders()
    {
        if (!Auth::user()->can('track order')) {
            abort(403);
        }
        $orders = Auth::user()->orders;
        return view('customer.orders', compact('orders'));
    }

    public function placeOrder(Request $request)
    {
        if (!Auth::user()->can('place order')) {
            abort(403);
        }
        // Logic tạo order từ cart, thanh toán (tích hợp Stripe/PayPal sau)
        // Ví dụ đơn giản:
        $cartItems = Auth::user()->cartItems;
        $total = $cartItems->sum('subtotal');
        $order = Order::create([
            'customer_id' => Auth::id(),
            'total_amount' => $total,
            'order_number' => Order::generateOrderNumber(),
            'shipping_address' => $request->shipping_address,
            'payment_method' => $request->payment_method,
        ]);
        foreach ($cartItems as $item) {
            $order->orderItems()->create([
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->product->current_price,
            ]);
            $item->delete(); // Xóa cart sau đặt hàng
        }
        return redirect()->route('customer.orders')->with('success', 'Đặt hàng thành công!');
    }

    
}
