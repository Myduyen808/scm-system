@extends('layouts.app')

@section('title', 'Chi Tiết Đơn Hàng')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="mb-0">
                    <i class="fas fa-shopping-cart text-primary"></i> Chi Tiết Đơn Hàng #{{ $order->order_number }}
                </h1>
                <a href="{{ route('admin.orders') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Order Details -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle"></i> Thông Tin Đơn Hàng
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Mã đơn hàng:</strong> {{ $order->order_number }}</p>
                            <p><strong>Khách hàng:</strong> {{ $order->customer ? $order->customer->name : 'Khách vãng lai' }}</p>
                            <p><strong>Email:</strong> {{ $order->customer ? $order->customer->email : 'N/A' }}</p>
                            <p><strong>Ngày đặt:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Địa chỉ giao:</strong> {{ $order->shipping_address }}</p>
                            <p><strong>Phương thức thanh toán:</strong> {{ $order->payment_method }}</p>
                            <p><strong>Trạng thái thanh toán:</strong>
                                <span class="badge {{ $order->payment_status == 'paid' ? 'bg-success' : ($order->payment_status == 'failed' ? 'bg-danger' : 'bg-warning') }}">
                                    {{ $order->payment_status == 'paid' ? 'Đã thanh toán' : ($order->payment_status == 'failed' ? 'Thất bại' : 'Chờ thanh toán') }}
                                </span>
                            </p>
                            <p><strong>Trạng thái đơn hàng:</strong>
                                <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                                        <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                                        <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Đã giao</option>
                                        <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Hoàn thành</option>
                                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                                    </select>
                                </form>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-box"></i> Sản Phẩm Trong Đơn Hàng
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Ảnh</th>
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
                                        <img src="{{ $item->product->image ? asset('storage/' . $item->product->image) : 'https://via.placeholder.com/60' }}"
                                             alt="{{ $item->product->name }}"
                                             class="img-thumbnail"
                                             style="width: 60px; height: 60px; object-fit: cover;">
                                    </td>
                                    <td>
                                        <strong>{{ $item->product->name }}</strong>
                                        <br><small class="text-muted">SKU: {{ $item->product->sku }}</small>
                                    </td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>₫{{ number_format($item->price, 0, ',', '.') }}</td>
                                    <td><strong>₫{{ number_format($item->subtotal, 0, ',', '.') }}</strong></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">Không có sản phẩm trong đơn hàng.</td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-end"><strong>Tổng cộng:</strong></td>
                                    <td><strong class="text-success">₫{{ number_format($order->total_amount, 0, ',', '.') }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar with Actions -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-cog text-primary"></i> Thao Tác
                    </h6>
                </div>
                <div class="card-body">
                    @if($order->status != 'cancelled' && $order->status != 'delivered')
                    <form action="{{ route('admin.orders.cancel', $order) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100 mb-2">
                            <i class="fas fa-trash"></i> Hủy Đơn Hàng
                        </button>
                    </form>
                    @endif
                    <a href="{{ route('admin.orders') }}" class="btn btn-secondary w-100">
                        <i class="fas fa-arrow-left"></i> Quay lại danh sách
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
