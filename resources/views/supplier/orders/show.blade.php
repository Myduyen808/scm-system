@extends('layouts.app')

@section('title', 'Chi Tiết Đơn Hàng - Nhà Cung Cấp')

@section('content')
<div class="container">
    <h1 class="mb-4"><i class="fas fa-shopping-cart"></i> Chi Tiết Đơn Hàng #{{ $order->order_number }}</h1>
    <!-- Flash Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="card mb-4 fade-in">
        <div class="card-body">
            <p><strong>Khách hàng:</strong> {{ $order->customer->name ?? 'Không xác định' }}</p>
            <p><strong>Tổng tiền:</strong> ₫{{ number_format($order->total_amount, 0, ',', '.') }}</p>
            <p><strong>Trạng thái:</strong> {{ $order->status }}</p>
            <p><strong>Ngày đặt:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
            @if($order->shipping_address)
                <p><strong>Địa chỉ giao hàng:</strong> {{ $order->shipping_address }}</p>
            @endif
        </div>
    </div>
    <h3>Sản phẩm</h3>
    <div class="card fade-in">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Hình ảnh</th> <!-- Thêm cột mới -->
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
                            <td>
                                @if($item->product->image)
                                    <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" style="width: 50px; height: 50px; object-fit: cover;" class="img-thumbnail">
                                @else
                                    <span>Không có hình ảnh</span>
                                @endif
                            </td>
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
    @if(in_array($order->status, ['pending', 'processing']))
    <form action="{{ route('supplier.orders.updateStatus', $order->id) }}" method="POST" class="mt-3">
        @csrf
        <div class="mb-3">
            <label for="status" class="form-label">Cập nhật trạng thái:</label>
            <select name="status" id="status" class="form-control" required>
                <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Đã giao</option>
                <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Đã giao đến khách</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary btn-sm">Cập nhật</button>
    </form>
    @endif
    <a href="{{ route('supplier.orders') }}" class="btn btn-secondary mt-3"><i class="fas fa-arrow-left"></i> Quay lại</a>
</div>
@endsection
