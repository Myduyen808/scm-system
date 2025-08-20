<?php

namespace App\Http\Controllers;

use App\Services\PaypalService;
use App\Services\MomoService;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Review;
use App\Models\Address;
use App\Models\Payment;
use App\Models\SupportTicket;
use App\Models\Promotion;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use App\Models\Cart;
use Stripe\PaymentIntent;
use Illuminate\Support\Facades\DB;


class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:customer');
    }

    public function home()
    {
        $featuredProducts = Product::where('is_approved', true)
            ->where('is_active', true)
            ->withCount('orderItems')
            ->with('inventory')
            ->whereHas('inventory', function ($query) {
                $query->where('stock', '>', 0);
            })
            ->orderBy('order_items_count', 'desc')
            ->take(4)
            ->get();

        $pendingOrders = Order::where('customer_id', Auth::id())
            ->whereIn('status', ['pending', 'processing', 'shipped'])
            ->count();

        $cartCount = Cart::where('user_id', Auth::id())->sum('quantity');

        return view('customer.home', compact('featuredProducts', 'pendingOrders', 'cartCount'));
    }

    public function products(Request $request)
    {
        $user = auth()->user();
        $cartCount = $user->cartItems()->sum('quantity');
        $query = Product::where('is_approved', true)
            ->where('is_active', true);

        if ($request->input('search')) {
            $query->where('name', 'like', '%' . $request->input('search') . '%')
                ->orWhere('sku', 'like', '%' . $request->input('search') . '%');
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(12);
        return view('customer.products.index', compact('products', 'cartCount'));
    }

    public function cart()
    {
        $cartItems = Cart::with(['product', 'product.inventory', 'product.promotions'])
            ->where('user_id', Auth::id())
            ->get();

        $itemCount = $cartItems->sum('quantity');
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
        $product = Product::with('inventory')->findOrFail($id);

        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $quantity = $request->input('quantity', 1);

        if (!$product->inventory || $quantity > $product->inventory->stock) {
            return back()->with('error', 'Số lượng vượt quá tồn kho hiện có (' . ($product->inventory->stock ?? 0) . ')!');
        }
        if (!$product->is_approved || !$product->is_active) {
            return back()->with('error', 'Sản phẩm không khả dụng hoặc chưa được phê duyệt!');
        }

        try {
            $user = auth()->user();
            if (!$user) {
                return back()->with('error', 'Vui lòng đăng nhập để thêm vào giỏ hàng!');
            }

            $cartItem = Cart::firstOrCreate(
                ['user_id' => $user->id, 'product_id' => $id],
                ['quantity' => 0]
            );

            $newQuantity = min($cartItem->quantity + $quantity, $product->inventory->stock);
            if ($newQuantity <= 0) {
                return back()->with('error', 'Số lượng không hợp lệ!');
            }
            $cartItem->quantity = $newQuantity;
            $cartItem->save();

            $cartCount = $user->cartItems()->sum('quantity');
            return redirect()->back()->with('success', "Đã thêm $quantity sản phẩm vào giỏ hàng!")->with('cartCount', $cartCount);
        } catch (\Exception $e) {
            \Log::error('Lỗi khi thêm vào giỏ hàng: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'product_id' => $id,
                'quantity' => $quantity,
                'stack_trace' => $e->getTraceAsString(),
            ]);
            return back()->with('error', 'Đã xảy ra lỗi khi thêm vào giỏ hàng. Vui lòng thử lại!');
        }
    }

    public function updateCart(Request $request, $id)
    {
        try {
            // Validate dữ liệu
            $request->validate([
                'quantity' => 'required|integer|min:1',
            ]);

            // Tìm cart item dựa trên id của cart (khóa chính)
            $cartItem = Cart::where('user_id', Auth::id())->where('id', $id)->firstOrFail();

            // Kiểm tra inventory
            if (!$cartItem->product->inventory) {
                $message = 'Sản phẩm không có thông tin tồn kho!';
                return $request->ajax() ? response()->json(['success' => false, 'message' => $message], 400) : back()->with('error', $message);
            }
            if ($request->quantity > $cartItem->product->inventory->stock) {
                $message = 'Số lượng vượt quá tồn kho!';
                return $request->ajax() ? response()->json(['success' => false, 'message' => $message], 400) : back()->with('error', $message);
            }
            if (!$cartItem->product->is_approved || !$cartItem->product->is_active) {
                $message = 'Sản phẩm không khả dụng!';
                return $request->ajax() ? response()->json(['success' => false, 'message' => $message], 400) : back()->with('error', $message);
            }

            // Cập nhật số lượng
            $cartItem->quantity = $request->quantity;
            $cartItem->save();

            // Cập nhật tổng số lượng giỏ hàng
            $cartCount = Cart::where('user_id', Auth::id())->sum('quantity');

            // Phản hồi
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cập nhật thành công!',
                    'cartCount' => $cartCount,
                    'newTotal' => ($cartItem->product->current_price ?? 0) * $cartItem->quantity,
                ]);
            }

            return redirect()->route('customer.cart')
                ->with('success', 'Đã cập nhật giỏ hàng!')
                ->with('cartCount', $cartCount);
        } catch (\Exception $e) {
            \Log::error('Lỗi cập nhật giỏ hàng: ' . $e->getMessage(), ['id' => $id, 'quantity' => $request->quantity]);
            $message = 'Đã xảy ra lỗi khi cập nhật giỏ hàng!';
            return $request->ajax() ? response()->json(['success' => false, 'message' => $message], 500) : back()->with('error', $message);
        }
    }

    public function removeFromCart($id)
    {
        $cartItem = Cart::where('user_id', Auth::id())->where('id', $id)->first();
        if ($cartItem) {
            $cartItem->delete();
            $cartCount = Cart::where('user_id', Auth::id())->sum('quantity');
            return redirect()->route('customer.cart')->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng!')->with('cartCount', $cartCount);
        }
        return redirect()->route('customer.cart')->with('error', 'Không tìm thấy mục để xóa!');
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

    public function placeOrder(Request $request)
    {
        $cartItems = Cart::with(['product', 'product.inventory'])
            ->where('user_id', Auth::id())
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('customer.cart')->with('error', 'Giỏ hàng trống!');
        }

        $request->validate([
            'address_id' => 'required|exists:addresses,id,user_id,' . Auth::id()
        ]);

        $address = Address::where('user_id', Auth::id())->findOrFail($request->address_id);

        $total = $cartItems->sum(function ($item) {
            return $item->product->current_price * $item->quantity;
        });

        // Quy đổi từ VND sang USD (1 USD = 26,351 VND)
        $exchangeRate = 26351;
        $totalInUsd = $total / $exchangeRate;

        if ($totalInUsd > 999999.99) {
            return back()->with('error', 'Tổng tiền vượt quá giới hạn thanh toán ($999,999.99). Vui lòng giảm số lượng hoặc liên hệ hỗ trợ!');
        }

        DB::beginTransaction();
        try {
            // Tạo order
            $order = Order::create([
                'order_number'     => 'ORD-' . time(),
                'customer_id'      => Auth::id(),
                'total_amount'     => $total, // VND
                'status'           => 'pending',
                'shipping_address' => $address->address_line,
                'payment_method'   => 'stripe',
                'payment_status'   => 'pending',
            ]);

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $item->product_id,
                    'quantity'   => $item->quantity,
                    'price'      => $item->product->current_price,
                ]);
                $item->product->inventory->decrement('stock', $item->quantity);
            }

            // Lưu Payment record
            Payment::create([
                'order_id'       => $order->id,
                'payment_method' => 'stripe',
                'amount'         => $total,
                'currency'       => 'VND',
                'transaction_id' => null,
                'status'         => 'pending',
            ]);

            // Xóa giỏ hàng
            Cart::where('user_id', Auth::id())->delete();

            // Stripe config
            $stripeSecret = env('STRIPE_SECRET_KEY');
            if (empty($stripeSecret)) {
                throw new \Exception('Stripe Secret Key không tồn tại hoặc chưa được cấu hình trong .env');
            }

            Stripe::setApiKey($stripeSecret);

            // Tạo PaymentIntent
            $paymentIntent = PaymentIntent::create([
                'amount'   => max(1, (int) round($totalInUsd * 100)), // phải >= 1 cent
                'currency' => 'usd',
                'metadata' => [
                    'order_id' => $order->id
                ],
            ]);

            DB::commit();

            return view('customer.checkout.confirm', compact('order', 'paymentIntent'));

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Lỗi đặt hàng: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function processPayment(Request $request, $orderId)
        {
            $order = Order::findOrFail($orderId);
            if ($order->customer_id !== Auth::id()) {
                abort(403);
            }

            $paymentMethod = $request->input('payment_method');
            $totalInUsd = $order->total_amount / 26351; // Chuyển VND sang USD

            if ($paymentMethod === 'stripe') {
                Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
                $paymentIntent = PaymentIntent::create([
                    'amount' => (int)($totalInUsd * 100),
                    'currency' => 'usd',
                    'metadata' => ['order_id' => $order->id],
                ]);
                return view('customer.checkout.confirm', compact('order', 'paymentIntent'));
            } elseif ($paymentMethod === 'paypal') {
                $paypalService = new PaypalService(); // Bây giờ sẽ nhận diện được
                $approvalUrl = $paypalService->createPayment($order, $totalInUsd);
                return redirect($approvalUrl);
            } elseif ($paymentMethod === 'momo') {
                $momoService = new MomoService();
                $payUrl = $momoService->createPayment($order, $order->total_amount);
                return redirect($payUrl);
            }

            return back()->with('error', 'Phương thức thanh toán không hợp lệ.');
        }


    public function paymentSuccess(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);
        if ($order->customer_id !== Auth::id()) {
            abort(403);
        }

        $paymentIntentId = $request->input('payment_intent_id');
        if ($paymentIntentId) {
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
            $paymentIntent = \Stripe\PaymentIntent::retrieve($paymentIntentId);

            if ($paymentIntent->status === 'succeeded') {
                $order->payment_status = 'paid';
                $order->status = 'processing';
                $order->save();

                // Cập nhật hoặc tạo bản ghi payment
                $order->payment()->updateOrCreate(
                    ['order_id' => $order->id],
                    [
                        'transaction_id' => $paymentIntentId,
                        'status' => 'paid',
                        'amount' => $order->total_amount
                    ]
                );

                return redirect()->route('customer.orders.show', $order->id)->with('success', 'Thanh toán Stripe thành công!');
            }
        }

        return back()->with('error', 'Thanh toán không thành công.');
    }

    public function orders(Request $request)
    {
        $query = Order::where('customer_id', Auth::id());

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

        DB::beginTransaction();
        try {
            foreach ($order->orderItems as $item) {
                $item->product->inventory->increment('stock', $item->quantity);
            }
            $order->update(['status' => 'cancelled']);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Lỗi khi hủy đơn hàng: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi khi hủy đơn hàng!');
        }

        return redirect()->route('customer.orders')->with('success', 'Đã hủy đơn hàng!');
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
    public function showAddress($id) {
        $address = Address::findOrFail($id);
        return view('customer.addresses.show', compact('address'));
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
            Address::where('user_id', Auth::id())->where('id', '!=', $id)->update(['is_default' => false]);
        }

        return redirect()->route('customer.addresses.index')->with('success', 'Đã cập nhật địa chỉ!');
    }

    public function deleteAddress($id)
    {
        $address = Address::where('user_id', Auth::id())->findOrFail($id);
        $address->delete();
        return redirect()->route('customer.addresses.index')->with('success', 'Đã xóa địa chỉ!');
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

        $ticket->update(['status' => 'pending']);
        return redirect()->route('customer.support.show', $id)->with('success', 'Đã gửi phản hồi!');
    }

    public function promotions()
    {
        $promotions = Promotion::where('is_active', true)->orderBy('expiry_date', 'desc')->get();
        return view('customer.promotions', compact('promotions'));
    }

    public function showProduct($id)
    {
        $product = Product::findOrFail($id);
        if (!$product->is_approved || !$product->is_active) {
            abort(404);
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
        if ($order->status !== 'pending' || $order->payment_status !== 'paid') {
            return redirect()->back()->with('error', 'Không thể xác nhận đơn hàng!');
        }

        $order->updateStatus('processing');
        return redirect()->route('customer.orders.show', $order->id)->with('success', 'Đơn hàng đã được xác nhận và đang xử lý!');
    }

    public function trackOrder(Request $request, $order_number = null)
    {
        $order = null;
        $errorMessage = null;

        // Lấy order_number từ route hoặc query string
        $order_number = $order_number ?? $request->input('order_number');
        \Log::info('trackOrder called with order_number: ' . ($order_number ?? 'null') . ' for user: ' . Auth::id());

        if ($order_number) {
            $order = Order::where('customer_id', Auth::id())
                ->where('order_number', 'like', '%' . $order_number . '%')
                ->with('orderItems.product')
                ->first();

            if (!$order) {
                $errorMessage = 'Không tìm thấy đơn hàng với mã "' . $order_number . '". Vui lòng kiểm tra lại hoặc liên hệ hỗ trợ.';
                \Log::warning('No order found for order_number: ' . $order_number . ' for user: ' . Auth::id());
            } else {
                \Log::info('Order found: ' . $order->order_number);
            }
        } else {
            $errorMessage = 'Không có mã đơn hàng được cung cấp. Vui lòng kiểm tra lại từ danh sách đơn hàng.';
        }

        return view('customer.orders.track', compact('order', 'errorMessage'));
    }
}
