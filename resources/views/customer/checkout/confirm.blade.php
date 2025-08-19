@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Xác Nhận Thanh Toán</h1>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <p>Mã đơn hàng: {{ $order->order_number }}</p>
    <p>Tổng tiền: {{ number_format($order->total_amount, 0, ',', '.') }} đ</p>
    <p>Địa chỉ giao hàng: {{ $order->shipping_address }}</p>
    <p>Trạng thái: {{ $order->payment_status }}</p>

    <!-- Hiển thị form thanh toán Stripe (client-side) -->
    <form action="{{ route('customer.payment.success', $order->id) }}" method="POST" id="payment-form">
        @csrf
        <input type="hidden" name="payment_intent_client_secret" value="{{ $paymentIntent->client_secret }}">
        <button type="submit" class="btn btn-primary">Hoàn tất thanh toán</button>
    </form>

    <script src="https://js.stripe.com/v3/"></script>
    <script>
        const stripe = Stripe('{{ env('STRIPE_PUBLISHABLE_KEY') }}');
        const elements = stripe.elements();
        const paymentForm = document.getElementById('payment-form');

        paymentForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            const { error } = await stripe.confirmPayment({
                elements,
                clientSecret: '{{ $paymentIntent->client_secret }}',
                confirmParams: {
                    payment_method_data: {
                        type: 'card',
                    },
                },
            });

            if (error) {
                alert(error.message);
            } else {
                paymentForm.submit();
            }
        });
    </script>
</div>
@endsection
