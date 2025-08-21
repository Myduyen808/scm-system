@extends('layouts.app')

@section('title', 'Chi Tiết Đơn Hàng - Khách Hàng')

@section('content')
<div class="container py-5">
    <h1 class="mb-4"><i class="fas fa-list-alt"></i> Chi Tiết Đơn Hàng #{{ $order->order_number }}</h1>
    <div class="card fade-in shadow-sm">
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

            <div class="mb-4">
                <p><strong>Trạng thái:</strong> <span class="badge bg-{{ $order->status === 'processing' ? 'primary' : 'secondary' }}">{{ $order->status }}</span></p>
                <p><strong>Trạng thái thanh toán:</strong> <span class="badge bg-{{ $order->payment_status === 'paid' ? 'success' : 'warning' }}">{{ $order->payment_status }}</span></p>
                <p><strong>Tổng tiền:</strong> ₫{{ number_format($order->total_amount, 0, ',', '.') }}</p>
                <p><strong>Địa chỉ giao hàng:</strong> {{ $order->shipping_address ?? 'Chưa cập nhật' }}</p>
                <p><strong>Ngày đặt:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
            </div>

            <h5 class="mt-4">Sản phẩm trong đơn hàng</h5>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Hình ảnh</th>
                        <th>Sản phẩm</th>
                        <th>Số lượng</th>
                        <th>Giá</th>
                        <th>Tổng</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($order->orderItems as $item)
                        <tr>
                            <td>
                                @if ($item->product->image)
                                    <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name ?? 'Hình ảnh sản phẩm' }}" class="img-fluid" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                @else
                                    <span class="text-muted">Không có ảnh</span>
                                @endif
                            </td>
                            <td>{{ $item->product->name ?? 'Sản phẩm không tồn tại' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>₫{{ number_format($item->price, 0, ',', '.') }}</td>
                            <td>₫{{ number_format($item->total ?? ($item->price * $item->quantity), 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Không có sản phẩm trong đơn hàng này.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if ($order->orderItems->isNotEmpty())
                <p class="text-end fw-bold">Tổng cộng: ₫{{ number_format($order->orderItems->sum(function ($item) {
                    return $item->total ?? ($item->price * $item->quantity);
                }), 0, ',', '.') }}</p>
            @endif

            <div class="mt-4">
                <a href="{{ route('customer.orders') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
                @if ($order->status === 'processing' && $order->payment_status === 'paid')
                    <a href="{{ route('customer.orders.track', $order->order_number) }}" class="btn btn-dark ms-2">
                        <i class="fas fa-truck"></i> Theo dõi
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
