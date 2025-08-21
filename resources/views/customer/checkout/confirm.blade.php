@extends('layouts.app')

@section('title', 'Xác Nhận Thanh Toán - SCM System')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 text-center mb-4">
            <h1 class="display-5 fw-bold text-primary mt-3">Xác Nhận Thanh Toán</h1>
            <p class="text-muted">Hệ thống quản lý chuỗi cung ứng hiện đại và hiệu quả</p>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="mb-3">
                        <h4 class="card-title">Thông tin đơn hàng</h4>
                        <hr>
                        <p><strong>Tổng tiền:</strong> {{ number_format($total, 0, ',', '.') }} đ</p>
                        <p><strong>Địa chỉ giao hàng:</strong> {{ $address->address_line }}</p>

                        <h5 class="mt-3">Sản phẩm trong giỏ hàng</h5>
                        <div class="list-group">
                            @foreach ($cartItems as $item)
                                <div class="list-group-item d-flex align-items-center">
                                    <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" class="me-3" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                    <div>
                                        <p class="mb-0"><strong>{{ $item->product->name }}</strong></p>
                                        <small>Số lượng: {{ $item->quantity }} - Giá: {{ number_format($item->product->current_price, 0, ',', '.') }} đ</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Chọn phương thức thanh toán</label>
                        <select name="payment_method" id="payment_method" class="form-select" onchange="togglePaymentForm()">
                            <option value="stripe">Thẻ tín dụng (Stripe)</option>
                            <option value="paypal">PayPal</option>
                            <option value="momo">MoMo (Mock)</option>
                        </select>
                    </div>

                    <!-- Form thanh toán Stripe -->
                    <form action="{{ route('customer.payment.success') }}" method="POST" id="stripe-form" style="display: block;">
                        @csrf
                        <input type="hidden" name="payment_intent_id" id="payment-intent-id">

                        <div class="mb-3">
                            <label for="card-element" class="form-label">Thông tin thẻ tín dụng</label>
                            <div id="card-element" class="form-control" style="padding: 10px; border: 1px solid #ced4da; border-radius: 0.25rem;"></div>
                            <div id="card-errors" role="alert" class="text-danger mt-2"></div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100" id="pay-button-stripe">
                            <i class="fas fa-credit-card"></i> Thanh toán với Stripe
                        </button>
                    </form>

                    <!-- Nút PayPal -->
                    <form action="{{ route('customer.paypal.create') }}" method="POST" id="paypal-form" style="display: none;">
                        @csrf
                        <input type="hidden" name="cart_items" value="{{ json_encode($cartItems->map(function ($item) {
                            return [
                                'product_id' => $item->product_id,
                                'quantity' => $item->quantity,
                                'price' => $item->product->current_price ?? 0,
                            ];
                        })->toArray()) }}">
                        <input type="hidden" name="address" value="{{ $address->address_line }}">
                        <input type="hidden" name="total" value="{{ $total }}">
                        <button type="submit" class="btn btn-primary btn-lg w-100" id="pay-button-paypal">
                            <i class="fab fa-paypal"></i> Thanh toán với PayPal
                        </button>
                    </form>

                    <!-- Form thanh toán MoMo Mock -->
                    <form action="{{ route('customer.momo.create') }}" method="POST" id="momo-form" style="display: none;">
                        @csrf
                        <input type="hidden" name="cart_items" value="{{ json_encode($cartItems->map(function ($item) {
                            return [
                                'product_id' => $item->product_id,
                                'quantity' => $item->quantity,
                                'price' => $item->product->current_price ?? 0,
                            ];
                        })->toArray()) }}">
                        <input type="hidden" name="address" value="{{ $address->address_line }}">
                        <input type="hidden" name="total" value="{{ $total }}">
                        <button type="submit" class="btn btn-primary btn-lg w-100" id="pay-button-momo">
                            <i class="fab fa-momo"></i> Thanh toán với MoMo (Mock)
                        </button>
                    </form>

                    <div class="text-center mt-4">
                        <img src="{{ asset('images/checkout-illustration.png') }}" alt="Checkout Illustration" class="img-fluid rounded" style="max-width: 300px;">
                        <p class="text-muted mt-2">Hình ảnh minh họa quy trình thanh toán an toàn</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://js.stripe.com/v3/"></script>
    <script>
        const stripe = Stripe('{{ env('STRIPE_PUBLISHABLE_KEY') }}');
        const elements = stripe.elements();
        const cardElement = elements.create('card');
        const stripeForm = document.getElementById('stripe-form');
        const payButtonStripe = document.getElementById('pay-button-stripe');
        const cardErrors = document.getElementById('card-errors');
        const paymentIntentIdInput = document.getElementById('payment-intent-id');
        const paypalForm = document.getElementById('paypal-form');
        const momoForm = document.getElementById('momo-form');

        cardElement.mount('#card-element');

        stripeForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            payButtonStripe.disabled = true;
            payButtonStripe.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';

            const { paymentMethod, error } = await stripe.createPaymentMethod({
                type: 'card',
                card: cardElement,
            });

            if (error) {
                cardErrors.textContent = error.message;
                payButtonStripe.disabled = false;
                payButtonStripe.innerHTML = '<i class="fas fa-credit-card"></i> Thanh toán với Stripe';
            } else {
                const { error: confirmError, paymentIntent } = await stripe.confirmCardPayment(
                    '{{ $paymentIntent->client_secret }}', {
                        payment_method: paymentMethod.id,
                    }
                );

                if (confirmError) {
                    cardErrors.textContent = confirmError.message;
                    payButtonStripe.disabled = false;
                    payButtonStripe.innerHTML = '<i class="fas fa-credit-card"></i> Thanh toán với Stripe';
                } else {
                    paymentIntentIdInput.value = paymentIntent.id;
                    stripeForm.submit();
                }
            }
        });

        function togglePaymentForm() {
            const paymentMethod = document.getElementById('payment_method').value;
            if (paymentMethod === 'stripe') {
                stripeForm.style.display = 'block';
                paypalForm.style.display = 'none';
                momoForm.style.display = 'none';
            } else if (paymentMethod === 'paypal') {
                stripeForm.style.display = 'none';
                paypalForm.style.display = 'block';
                momoForm.style.display = 'none';
            } else if (paymentMethod === 'momo') {
                stripeForm.style.display = 'none';
                paypalForm.style.display = 'none';
                momoForm.style.display = 'block';
            }
        }

        togglePaymentForm();
    </script>
</div>
@endsection
