@extends('layouts.app')

@section('title', 'Xác Nhận Thanh Toán - SCM System')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <!-- Logo và tiêu đề -->
        <div class="col-12 text-center mb-4">
            <h1 class="display-5 fw-bold text-primary mt-3">Xác Nhận Thanh Toán</h1>
            <p class="text-muted">Hệ thống quản lý chuỗi cung ứng hiện đại và hiệu quả</p>
        </div>

        <!-- Thông tin đơn hàng -->
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
                        <p><strong>Mã đơn hàng:</strong> {{ $order->order_number }}</p>
                        <p><strong>Tổng tiền:</strong> {{ number_format($order->total_amount, 0, ',', '.') }} đ</p>
                        <p><strong>Địa chỉ giao hàng:</strong> {{ $order->shipping_address }}</p>
                        <p><strong>Trạng thái:</strong>
                            <span class="badge bg-warning text-dark">{{ $order->payment_status }}</span>
                        </p>

                        <!-- Danh sách sản phẩm -->
                        <h5 class="mt-3">Sản phẩm trong đơn hàng</h5>
                        <div class="list-group">
                            @foreach ($order->orderItems as $item)
                                <div class="list-group-item d-flex align-items-center">
                                    <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" class="me-3" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                    <div>
                                        <p class="mb-0"><strong>{{ $item->product->name }}</strong></p>
                                        <small>Số lượng: {{ $item->quantity }} - Giá: {{ number_format($item->price, 0, ',', '.') }} đ</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Form thanh toán với Stripe Elements -->
                    <form action="{{ route('customer.payment.success', $order->id) }}" method="POST" id="payment-form">
                        @csrf
                        <input type="hidden" name="payment_intent_id" id="payment-intent-id">

                        <!-- Stripe Card Element -->
                        <div class="mb-3">
                            <label for="card-element" class="form-label">Thông tin thẻ tín dụng</label>
                            <div id="card-element" class="form-control" style="padding: 10px; border: 1px solid #ced4da; border-radius: 0.25rem;"></div>
                            <div id="card-errors" role="alert" class="text-danger mt-2"></div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100" id="pay-button">
                            <i class="fas fa-credit-card"></i> Hoàn tất thanh toán
                        </button>
                    </form>

                    <!-- Hình ảnh minh họa -->
                    <div class="text-center mt-4">
                        <img src="{{ asset('images/checkout-illustration.png') }}" alt="Checkout Illustration" class="img-fluid rounded" style="max-width: 300px;">
                        <p class="text-muted mt-2">Hình ảnh minh họa quy trình thanh toán an toàn</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Stripe -->
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        const stripe = Stripe('{{ env('STRIPE_PUBLISHABLE_KEY') }}');
        const elements = stripe.elements();
        const cardElement = elements.create('card');
        const paymentForm = document.getElementById('payment-form');
        const payButton = document.getElementById('pay-button');
        const cardErrors = document.getElementById('card-errors');
        const paymentIntentIdInput = document.getElementById('payment-intent-id');

        // Mount card element
        cardElement.mount('#card-element');

        paymentForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            payButton.disabled = true;
            payButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';

            const { paymentMethod, error } = await stripe.createPaymentMethod({
                type: 'card',
                card: cardElement,
            });

            if (error) {
                cardErrors.textContent = error.message;
                payButton.disabled = false;
                payButton.innerHTML = '<i class="fas fa-credit-card"></i> Hoàn tất thanh toán';
            } else {
                const { error: confirmError, paymentIntent } = await stripe.confirmCardPayment(
                    '{{ $paymentIntent->client_secret }}', {
                        payment_method: paymentMethod.id,
                    }
                );

                if (confirmError) {
                    cardErrors.textContent = confirmError.message;
                    payButton.disabled = false;
                    payButton.innerHTML = '<i class="fas fa-credit-card"></i> Hoàn tất thanh toán';
                } else {
                    paymentIntentIdInput.value = paymentIntent.id;
                    paymentForm.submit();
                }
            }
        });
    </script>
</div>
@endsection
