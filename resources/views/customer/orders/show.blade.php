@extends('layouts.app')

@section('title', 'Chi Tiết Đơn Hàng - Khách Hàng')

@section('content')
<div class="container">
    <h1 class="mb-4"><i class="fas fa-list-alt"></i> Chi Tiết Đơn Hàng #{{ $order->order_number }}</h1>
    <div class="card fade-in">
        <div class="card-body">
            <p><strong>Trạng thái:</strong> {{ $order->status }}</p>
            <p><strong>Tổng tiền:</strong> ₫{{ number_format($order->total_amount, 0, ',', '.') }}</p>
            <p><strong>Ngày đặt:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
            <h5 class="mt-4">Sản phẩm trong đơn hàng</h5>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Số lượng</th>
                        <th>Giá</th>
                        <th>Tổng</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($order->orderItems as $item)
                    <tr>
                        <td>{{ $item->product->name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>₫{{ number_format($item->price, 0, ',', '.') }}</td>
                        <td>₫{{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center">Không có sản phẩm.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <a href="{{ route('customer.orders') }}" class="btn btn-secondary mt-3"><i class="fas fa-arrow-left"></i> Quay lại</a>
        </div>
    </div>
</div>
@endsection
