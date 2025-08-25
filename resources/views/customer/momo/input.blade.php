@extends('layouts.app')

@section('title', 'Thanh Toán MoMo - SCM System')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 text-center mb-4">
            <h1 class="display-5 fw-bold text-primary mt-3">Thanh Toán với MoMo</h1>
            <p class="text-muted">Hệ thống quản lý chuỗi cung ứng hiện đại và hiệu quả</p>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="mb-3">
                        <h4 class="card-title">Thông tin đơn hàng</h4>
                        <hr>
                        <p><strong>Tổng tiền:</strong> {{ number_format($order->total_amount, 0, ',', '.') }} đ</p>
                        <p><strong>Địa chỉ giao hàng:</strong> {{ $order->shipping_address }}</p>

                        <h5 class="mt-3">Sản phẩm trong giỏ hàng</h5>
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

                    <form action="{{ route('customer.momo.confirm', $order->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="transId" value="{{ 'mock_' . time() }}">

                        <div class="mb-3">
                            <label for="payment_method" class="form-label">Nhập thông tin thanh toán</label>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Số điện thoại MoMo</label>
                                <input type="text" name="phone" id="phone" class="form-control" placeholder="Nhập số điện thoại" required>
                            </div>
                            <div class="mb-3">
                                <label for="otp" class="form-label">Mã OTP (Mock: 123456)</label>
                                <input type="text" name="otp" id="otp" class="form-control" placeholder="Nhập mã OTP" required>
                                <small class="text-muted">Trong chế độ mock, hãy nhập "123456" để thử nghiệm.</small>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100" id="pay-button-momo">
                            <i class="fab fa-momo"></i> Xác nhận thanh toán với MoMo
                        </button>
                    </form>

                    <div class="text-center mt-4">
                        <img src="{{ asset('images/momo-illustration.png') }}" alt="MoMo Illustration" class="img-fluid rounded" style="max-width: 300px;">
                        <p class="text-muted mt-2">Hình ảnh minh họa quy trình thanh toán an toàn</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
