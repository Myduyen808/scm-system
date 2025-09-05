@extends('layouts.app')

@section('title', 'Theo Dõi Giao Hàng - Khách Hàng')

@section('content')
<div class="container">
    <h1 class="mb-4"><i class="fas fa-truck"></i> Theo Dõi Giao Hàng</h1>
    <div class="card fade-in">
        <div class="card-body">
            @if($errorMessage)
                <div class="alert alert-warning">
                    {{ $errorMessage }}
                </div>
            @endif
            <form method="GET" class="mb-4">
                <div class="input-group">
                    <input type="text" name="order_number" placeholder="Nhập mã đơn hàng..." value="{{ request()->input('order_number') }}" class="form-control">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Theo dõi</button>
                </div>
            </form>

            @if($order)
                <div class="card mt-3">
                    <div class="card-body">
                        <h5 class="card-title">Thông tin đơn hàng #{{ $order->order_number }}</h5>
                        <p><strong>Tổng tiền:</strong> ₫{{ number_format($order->total_amount, 0, ',', '.') }}</p>
                        <p><strong>Trạng thái:</strong> {{ $order->shippingStatusText }}</p>
                        <p><strong>Ngày đặt:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                        @if($order->delivered_at)
                            <p><strong>Ngày giao:</strong> {{ $order->delivered_at->format('d/m/Y H:i') }}</p>
                        @endif
                        @if($order->tracking_number)
                            <p><strong>Số theo dõi:</strong> {{ $order->tracking_number }}</p>
                        @endif
                        @if($order->shipping_note)
                            <p><strong>Ghi chú giao hàng:</strong> {{ $order->shipping_note }}</p>
                        @endif

                        <h4 class="mt-4">Sản phẩm</h4>
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
                                @forelse($order->orderItems as $item)
                                <tr>
                                    <td>
                                        @if($item->product && $item->product->image)
                                            <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name ?? 'Sản phẩm' }}" style="width: 50px; height: 50px; object-fit: cover;" class="img-thumbnail">
                                        @else
                                            <span>Không có hình ảnh</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->product->name ?? 'Không xác định' }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>₫{{ number_format($item->price, 0, ',', '.') }}</td>
                                    <td>₫{{ number_format($item->quantity * $item->price, 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center">Không có sản phẩm.</td></tr>
                                @endforelse
                            </tbody>
                        </table>

                        <div class="progress mt-3">
                            <div class="progress-bar" role="progressbar" style="width: {{
                                $order->status == 'delivered' ? '100%' :
                                ($order->status == 'shipped' ? '75%' :
                                ($order->status == 'processing' ? '50%' :
                                ($order->status == 'pending' ? '25%' : '0%')))
                            }}" aria-valuenow="{{
                                $order->status == 'delivered' ? 100 :
                                ($order->status == 'shipped' ? 75 :
                                ($order->status == 'processing' ? 50 :
                                ($order->status == 'pending' ? 25 : 0)))
                            }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <small class="text-muted">0%: Hủy, 25%: Chờ xử lý, 50%: Đang chuẩn bị, 75%: Đang vận chuyển, 100%: Hoàn thành</small>
                    </div>
                </div>
            @else
                @if(!$errorMessage)
                    <p class="text-center">Vui lòng nhập mã đơn hàng để bắt đầu theo dõi.</p>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
