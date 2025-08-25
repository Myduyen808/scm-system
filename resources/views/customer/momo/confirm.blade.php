@extends('layouts.app')

@section('title', 'Xác Nhận Thanh Toán MoMo - SCM System')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 text-center mb-4">
            <h1 class="display-5 fw-bold text-success mt-3">Xác Nhận Thanh Toán MoMo</h1>
            <p class="text-muted">Cảm ơn bạn đã sử dụng dịch vụ của chúng tôi!</p>
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
                        <h4 class="card-title">Thông tin thanh toán</h4>
                        <hr>
                        <p><strong>Mã giao dịch:</strong> {{ $transId ?? 'Chưa xác định' }}</p>
                        <p><strong>Tổng tiền:</strong> {{ number_format($order->total_amount ?? 0, 0, ',', '.') }} đ</p>
                        <p><strong>Địa chỉ giao hàng:</strong> {{ $order->shipping_address ?? 'Chưa có' }}</p>

                        <h5 class="mt-3">Sản phẩm đã đặt</h5>
                        <div class="list-group">
                            @foreach ($order->orderItems ?? [] as $item)
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

                    <div class="text-center mt-4">
                        <a href="{{ route('customer.orders.show', $order->id) }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-eye"></i> Xem chi tiết đơn hàng
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
