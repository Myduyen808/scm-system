@extends('layouts.app')

@section('title', 'Chi Tiết Đơn Hàng - Nhà Cung Cấp')

@section('content')
<div class="container">
    <h1 class="mb-4"><i class="fas fa-shopping-cart"></i> Chi Tiết Đơn Hàng #{{ $order->order_number }}</h1>
    <div class="card mb-4 fade-in">
        <div class="card-body">
            <p><strong>Khách hàng:</strong> {{ $order->user->name }}</p>
            <p><strong>Tổng tiền:</strong> ₫{{ number_format($order->total_amount, 0, ',', '.') }}</p>
            <p><strong>Trạng thái:</strong> {{ $order->status }}</p>
            <p><strong>Ngày đặt:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>
    <h3>Sản phẩm</h3>
    <div class="card fade-in">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Tên sản phẩm</th>
                        <th>Số lượng</th>
                        <th>Giá</th>
                        <th>Tổng</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->orderItems as $item)
                        @if($item->product->supplier_id == Auth::id())
                        <tr>
                            <td>{{ $item->product->name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>₫{{ number_format($item->price, 0, ',', '.') }}</td>
                            <td>₫{{ number_format($item->quantity * $item->price, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <a href="{{ route('supplier.orders') }}" class="btn btn-secondary mt-3"><i class="fas fa-arrow-left"></i> Quay lại</a>
</div>
@endsection
