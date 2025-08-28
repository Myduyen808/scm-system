@extends('layouts.app')

@section('title', 'Chi Tiết Đơn Hàng - Nhân Viên')

@section('content')
<div class="container">
    <h1><i class="fas fa-info-circle"></i> Chi Tiết Đơn Hàng #{{ $order->order_number }}</h1>
    <div class="card">
        <div class="card-body">
            <p><strong>Trạng thái:</strong> {{ $order->status }}</p>
            <p><strong>Tổng tiền:</strong> ₫{{ number_format($order->total_amount, 0, ',', '.') }}</p>
            <h4>Sản phẩm trong đơn:</h4>
            <table class="table">
                <thead>
                    <tr>
                        <th>Hình ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th>Số lượng</th>
                        <th>Giá</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($order->orderItems as $item)
                    <tr>
                        <td>
                            @if($item->product && $item->product->image)
                                <img src="{{ Storage::url($item->product->image) }}" alt="{{ $item->product->name }}" class="img-thumbnail" style="max-width: 100px; max-height: 100px;">
                            @else
                                <img src="{{ asset('images/placeholder.jpg') }}" alt="No image" class="img-thumbnail" style="max-width: 100px; max-height: 100px;">
                            @endif
                        </td>
                        <td>{{ $item->product->name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>₫{{ number_format($item->price, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center">Không có sản phẩm</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <a href="{{ route('employee.orders') }}" class="btn btn-secondary">Quay lại</a>
        </div>
    </div>
</div>
@endsection
