<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Review;
use App\Models\Address;
use App\Models\SupportTicket;
use App\Models\Promotion;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use App\Models\Cart; // Thêm model Cart
use Stripe\PaymentIntent;
use Illuminate\Support\Facades\DB; // Để transaction

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:customer');
    }

    public function home()
    {
        // Thêm đề xuất sản phẩm hot (dựa trên số lượng bán)
        $featuredProducts = Product::where('is_approved', true)
            ->where('is_active', true) // Thêm kiểm tra is_active
            ->withCount('orderItems') // Đếm số lần bán
            ->with('inventory')
            ->whereHas('inventory', function ($query) {
                $query->where('stock', '>', 0);
            })
            ->orderBy('order_items_count', 'desc')
            ->take(4)
            ->get();

        return view('customer.home', compact('featuredProducts'));
    }

    public function products(Request $request)
    {
        $query = Product::where('is_approved', true)
            ->where('is_active', true); // Thêm kiểm tra is_active

        if ($request->input('search')) {
            $query->where('name', 'like', '%' . $request->input('search') . '%')
                ->orWhere('sku', 'like', '%' . $request->input('search') . '%');
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(12);
        return view('customer.products.index', compact('products'));
    }

    public function cart()
    {
        $cartItems = Cart::with(['product', 'product.inventory', 'product.promotions'])
            ->where('user_id', Auth::id())
            ->get();

        $itemCount = $cartItems->sum('quantity'); // Tổng số lượng sản phẩm
        $total = $cartItems->sum(function ($item) {
            return $item->product ? $item->product->current_price * $item->quantity : 0;
        });

        $discountedTotal = $cartItems->sum(function ($item) {
            if (!$item->product) return 0;
            $price = $item->product->current_price * $item->quantity;
            $promotion = $item->product->promotions->first();
            return $promotion && $promotion->is_valid ? $promotion->getDiscountedPriceAttribute($price) : $price;
        });

        return view('customer.cart.index', compact('cartItems', 'total', 'discountedTotal', 'itemCount'));
    }

    public function addToCart(Request $request, $id)
    {
        // Eager load relationship inventory
        $product = Product::with('inventory')->findOrFail($id);
        $quantity = $request->input('quantity', 1);

        // Kiểm tra tồn kho và trạng thái sản phẩm
        if ($product->inventory && $quantity > $product->inventory->stock) {
            return back()->with('error', 'Số lượng vượt quá tồn kho!');
        }
        if (!$product->is_approved || !$product->is_active) {
            return back()->with('error', 'Sản phẩm không khả dụng!');
        }

        $cartItem = Cart::firstOrCreate(
            ['user_id' => Auth::id(), 'product_id' => $id],
            ['quantity' => 0]
        );
        $cartItem->quantity += $quantity;
        $cartItem->save();

        return redirect()->back()->with('success', 'Đã thêm vào giỏ hàng!');
    }

    public function updateCart(Request $request, $id)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);
        $cartItem = Cart::where('user_id', Auth::id())->where('product_id', $id)->firstOrFail();

        if ($request->quantity > $cartItem->product->inventory->stock) {
            return back()->with('error', 'Số lượng vượt quá tồn kho!');
        }
        if (!$cartItem->product->is_approved || !$cartItem->product->is_active) {
            return back()->with('error', 'Sản phẩm không khả dụng!');
        }

        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        return redirect()->route('customer.cart')->with('success', 'Đã cập nhật giỏ hàng!');
    }

    public function removeFromCart($id)
    {
        Cart::where('user_id', Auth::id())->where('product_id', $id)->delete();
        return redirect()->route('customer.cart')->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng!');
    }

    public function placeOrder(Request $request)
    {
        $cartItems = Cart::with('product')->where('user_id', Auth::id())->get();
        if ($cartItems->isEmpty()) {
            return redirect()->route('customer.cart')->with('error', 'Giỏ hàng trống!');
        }

        $total = $cartItems->sum(function ($item) {
            return $item->product->current_price * $item->quantity;
        });

        $discountedTotal = $cartItems->sum(function ($item) {
            $price = $item->product->current_price * $item->quantity;
            $promotion = $item->product->promotions->first();
            return $promotion && $promotion->is_valid ? $promotion->getDiscountedPriceAttribute($price) : $price;
        });

        $addressId = $request->input('address_id');
        $address = Address::where('user_id', Auth::id())->findOrFail($addressId);

        DB::beginTransaction();
        try {
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'customer_id' => Auth::id(),
                'total_amount' => $discountedTotal > 0 ? $discountedTotal : $total,
                'status' => 'pending',
                'shipping_address' => $address->address_line,
                'payment_method' => 'stripe',
                'payment_status' => 'pending',
            ]);

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->current_price,
                ]);
                $item->product->inventory->decrement('stock', $item->quantity);
            }

            Stripe::setApiKey(env('STRIPE_SECRET'));
            $paymentIntent = PaymentIntent::create([
                'amount' => ($discountedTotal > 0 ? $discountedTotal : $total) * 100,
                'currency' => 'usd',
                'metadata' => ['order_id' => $order->id],
            ]);

            $order->update(['payment_status' => 'processing']);
            Cart::where('user_id', Auth::id())->delete();

            DB::commit();

            return view('customer.checkout.confirm', compact('order', 'paymentIntent'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function checkout()
    {
        $cartItems = Cart::with(['product', 'product.inventory', 'product.promotions'])
            ->where('user_id', Auth::id())
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('customer.cart')->with('error', 'Giỏ hàng trống!');
        }

        $total = $cartItems->sum(function ($item) {
            return $item->product ? $item->product->current_price * $item->quantity : 0;
        });

        $discountedTotal = $cartItems->sum(function ($item) {
            if (!$item->product) return 0;
            $price = $item->product->current_price * $item->quantity;
            $promotion = $item->product->promotions->first();
            return $promotion && $promotion->is_valid ? $promotion->getDiscountedPriceAttribute($price) : $price;
        });

        $addresses = Auth::user()->addresses;
        return view('customer.checkout.index', compact('cartItems', 'total', 'discountedTotal', 'addresses'));
    }

    public function paymentSuccess(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);
        if ($order->customer_id !== Auth::id()) {
            abort(403);
        }

        // Xác thực thanh toán từ Stripe (cần webhook để kiểm tra status thực tế)
        $order->update(['payment_status' => 'paid', 'status' => 'processing']);
        return redirect()->route('customer.orders.show', $order->id)->with('success', 'Thanh toán thành công!');
    }

    public function orders(Request $request)
    {
        $query = Order::where('user_id', Auth::id());

        if ($request->input('search')) {
            $query->where('order_number', 'like', '%' . $request->input('search') . '%');
        }
        if ($request->input('status')) {
            $query->where('status', $request->input('status'));
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(10);
        return view('customer.orders.index', compact('orders'));
    }

    public function cancelOrder($id)
    {
        $order = Order::where('customer_id', Auth::id())->findOrFail($id);
        if ($order->status !== 'pending') {
            return redirect()->back()->with('error', 'Không thể hủy đơn hàng này!');
        }

        // Hoàn kho (tạm, sau dùng RabbitMQ)
        foreach ($order->orderItems as $item) {
            $item->product->increment('stock_quantity', $item->quantity);
        }

        $order->update(['status' => 'cancelled']);
        return redirect()->route('customer.orders')->with('success', 'Đã hủy đơn hàng!');
    }

    public function trackOrder(Request $request)
    {
        $order = null;
        if ($request->input('order_number')) {
            $order = Order::where('user_id', Auth::id())->where('order_number', $request->input('order_number'))->first();
        }
        return view('customer.orders.track', compact('order'));
    }

    public function createReview($productId)
    {
        $product = Product::findOrFail($productId);
        return view('customer.reviews.create', compact('product'));
    }

    public function storeReview(Request $request, $productId)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        Review::create([
            'user_id' => Auth::id(),
            'product_id' => $productId,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'status' => 'pending',
        ]);

        return redirect()->route('customer.products')->with('success', 'Đánh giá đã được gửi!');
    }

    public function addresses()
    {
        $addresses = Address::where('user_id', Auth::id())->get();
        return view('customer.addresses.index', compact('addresses'));
    }

    public function createAddress()
    {
        return view('customer.addresses.create');
    }

    public function storeAddress(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'address_line' => 'required|string|max:1000',
            'is_default' => 'boolean',
        ]);

        $address = Address::create([
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'address_line' => $validated['address_line'],
            'is_default' => $validated['is_default'] ?? false,
        ]);

        if ($address->is_default) {
            Address::where('user_id', Auth::id())->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        return redirect()->route('customer.addresses.index')->with('success', 'Đã thêm địa chỉ!');
    }

    public function editAddress($id)
    {
        $address = Address::where('user_id', Auth::id())->findOrFail($id);
        return view('customer.addresses.edit', compact('address'));
    }

    public function updateAddress(Request $request, $id)
    {
        $address = Address::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'address_line' => 'required|string|max:1000',
            'is_default' => 'boolean',
        ]);

        $address->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'address_line' => $validated['address_line'],
            'is_default' => $validated['is_default'] ?? false,
        ]);

        if ($address->is_default) {
            Address::where('user_id', Auth::id())->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        return redirect()->route('customer.addresses.index')->with('success', 'Đã cập nhật địa chỉ!');
    }

    public function deleteAddress($id)
    {
        $address = Address::where('user_id', Auth::id())->findOrFail($id);
        $address->delete();
        return redirect()->route('customer.addresses')->with('success', 'Đã xóa địa chỉ!');
    }

    public function support()
    {
        $tickets = SupportTicket::where('user_id', Auth::id())->orderBy('created_at', 'desc')->paginate(10);
        return view('customer.support.index', compact('tickets'));
    }

    public function createSupport()
    {
        return view('customer.support.create');
    }

    public function storeSupport(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
        ]);

        SupportTicket::create([
            'user_id' => Auth::id(),
            'ticket_number' => 'TICK-' . time(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'status' => 'open',
        ]);

        return redirect()->route('customer.support.index')->with('success', 'Đã tạo yêu cầu hỗ trợ!');
    }

    public function showSupport($id)
    {
        $ticket = SupportTicket::where('user_id', Auth::id())->findOrFail($id);
        return view('customer.support.show', compact('ticket'));
    }

    public function replySupport(Request $request, $id)
    {
        $ticket = SupportTicket::where('user_id', Auth::id())->findOrFail($id);
        $validated = $request->validate(['message' => 'required|string|max:1000']);

        // Giả sử có model SupportTicketReply
        $ticket->replies()->create([
            'user_id' => Auth::id(),
            'message' => $validated['message'],
        ]);

        $ticket->update(['status' => 'pending']); // Cập nhật status

        return redirect()->route('customer.support.show', $id)->with('success', 'Đã gửi phản hồi!');
    }

    public function promotions()
    {
        $promotions = Promotion::where('is_active', true)->orderBy('expiry_date', 'desc')->get();
        return view('customer.promotions', compact('promotions'));
    }

    public function showCheckout()
    {
        $cartItems = session()->get('cart', []);
        if (empty($cartItems)) {
            return redirect()->route('customer.cart')->with('error', 'Giỏ hàng trống!');
        }

        $total = 0;
        $discountedTotal = 0;
        foreach ($cartItems as $item) {
            $product = Product::find($item['id']);
            $subtotal = $item['price'] * $item['quantity'];
            $total += $subtotal;

            if ($product && $product->promotions->count() > 0) {
                $promotion = $product->promotions->first();
                if ($promotion->is_valid) {
                    $discountedSubtotal = $promotion->getDiscountedPriceAttribute($subtotal);
                    $discountedTotal += $discountedSubtotal;
                } else {
                    $discountedTotal += $subtotal;
                }
            } else {
                $discountedTotal += $subtotal;
            }
        }

        $addresses = Auth::user()->addresses;
        return view('customer.checkout.index', compact('cartItems', 'total', 'discountedTotal', 'addresses'));
    }

    public function showProduct($id)
    {
        $product = Product::findOrFail($id);
        if (!$product->is_approved || !$product->is_active) {
            abort(404); // Hoặc thông báo lỗi tùy ý
        }
        return view('customer.products.show', compact('product'));
    }

    public function showOrder($id)
    {
        $order = Order::where('customer_id', Auth::id())->findOrFail($id);
        return view('customer.orders.show', compact('order'));
    }

    public function confirmOrder($id)
    {
        $order = Order::where('customer_id', Auth::id())->findOrFail($id);
        // Logic xác nhận order (nếu cần)
        return view('customer.orders.confirm', compact('order'));
    }

    public function createTicket(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
        ]);

        SupportTicket::create([
            'user_id' => Auth::id(),
            'ticket_number' => 'TICK-' . time(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'status' => 'open',
        ]);

        return redirect()->route('customer.support')->with('success', 'Đã tạo yêu cầu hỗ trợ!');
    }

    public function showTicket($id)
    {
        $ticket = SupportTicket::where('user_id', Auth::id())->findOrFail($id);
        return view('customer.support.show', compact('ticket'));
    }

    public function replyTicket(Request $request, $id)
    {
        $ticket = SupportTicket::where('user_id', Auth::id())->findOrFail($id);
        $validated = $request->validate(['message' => 'required|string|max:1000']);

        // Giả sử có model SupportTicketReply
        $ticket->replies()->create([
            'user_id' => Auth::id(),
            'message' => $validated['message'],
        ]);

        $ticket->update(['status' => 'pending']);
        return redirect()->route('customer.support.show', $id)->with('success', 'Đã gửi phản hồi!');
    }
}
