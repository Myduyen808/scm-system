@extends('layouts.app')

@section('title', 'Quản Lý Đơn Hàng')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="mb-0">
                    <i class="fas fa-shopping-cart text-primary"></i> Quản Lý Đơn Hàng
                </h1>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.orders') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <input type="text"
                               name="search"
                               class="form-control"
                               placeholder="Tìm kiếm mã đơn hàng..."
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">Tất cả trạng thái</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                            <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Đã giao</option>
                            <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Hoàn thành</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-search"></i> Lọc
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('admin.orders') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-redo"></i> Làm mới
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card">
        <div class="card-body">
            <!-- Export Button -->
            <div class="mb-3">
                <a href="{{ route('admin.orders.export') }}" class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Xuất danh sách đơn hàng (Excel)
                </a>
                <a href="{{ route('admin.orders.stats') }}" class="btn btn-info">
                    <i class="fas fa-chart-line"></i> Thống kê doanh thu
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Mã đơn hàng</th>
                            <th>Hình ảnh sản phẩm</th> <!-- Thêm cột mới -->
                            <th>Khách hàng</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Thanh toán</th>
                            <th width="200">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td><code>{{ $order->order_number }}</code></td>
                            <td>
                                @foreach($order->orderItems as $item)
                                    <img src="{{ asset('storage/' . $item->product->image) }}"
                                         alt="{{ $item->product->name }}"
                                         style="width: 50px; height: 50px; object-fit: cover; margin-right: 5px;"
                                         class="img-thumbnail">
                                @endforeach
                            </td> <!-- Hiển thị hình ảnh sản phẩm -->
                            <td>
                                {{ $order->customer ? $order->customer->name : 'Khách vãng lai' }}
                                <br><small class="text-muted">{{ $order->customer ? $order->customer->email : '' }}</small>
                            </td>
                            <td><strong class="text-success">₫{{ number_format($order->total_amount, 0, ',', '.') }}</strong></td>
                            <td>
                                <select class="form-select form-select-sm order-status"
                                        data-order-id="{{ $order->id }}"
                                        style="width: 150px;">
                                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                                    <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                                    <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Đã giao</option>
                                    <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Hoàn thành</option>
                                    <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                                </select>
                            </td>
                            <td>
                                <span class="badge {{ $order->payment_status == 'paid' ? 'bg-success' : ($order->payment_status == 'failed' ? 'bg-danger' : 'bg-warning') }}">
                                    {{ $order->payment_status == 'paid' ? 'Đã thanh toán' : ($order->payment_status == 'failed' ? 'Thất bại' : 'Chờ thanh toán') }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('admin.orders.show', $order) }}"
                                       class="btn btn-outline-primary"
                                       title="Xem chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button type="button"
                                            class="btn btn-outline-success confirm-payment"
                                            data-order-id="{{ $order->id }}"
                                            data-order-number="{{ $order->order_number }}"
                                            title="Xác nhận thanh toán"
                                            {{ $order->payment_status == 'paid' ? 'disabled' : '' }}>
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button type="button"
                                            class="btn btn-outline-danger cancel-order"
                                            data-order-id="{{ $order->id }}"
                                            data-order-number="{{ $order->order_number }}"
                                            title="Hủy đơn hàng">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                                    <p>Không có đơn hàng nào được tìm thấy.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($orders->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $orders->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Cancel Confirmation Modal -->
    <div class="modal fade" id="cancelOrderModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Xác nhận hủy đơn hàng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Bạn có chắc muốn hủy đơn hàng <strong id="order-number"></strong>?
                    <br><small class="text-warning">Hành động này không thể hoàn tác!</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <form id="cancel-form" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Hủy Đơn Hàng</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Update order status
    $('.order-status').on('change', function() {
        const orderId = $(this).data('order-id');
        const newStatus = $(this).val();

        $.ajax({
            url: `/admin/orders/${orderId}/status`,
            method: 'PATCH',
            data: {
                _token: '{{ csrf_token() }}',
                status: newStatus
            },
            success: function(response) {
                showToast('success', response.message || 'Cập nhật trạng thái thành công!');
            },
            error: function() {
                showToast('error', 'Có lỗi xảy ra khi cập nhật trạng thái!');
            }
        });
    });

    // Cancel order
    $('.cancel-order').on('click', function() {
        const orderId = $(this).data('order-id');
        const orderNumber = $(this).data('order-number');
        $('#order-number').text(orderNumber);
        $('#cancel-form').attr('action', `/admin/orders/${orderId}`);
        $('#cancelOrderModal').modal('show');
    });

    // Confirm payment
    $('.confirm-payment').on('click', function() {
        const orderId = $(this).data('order-id');
        const orderNumber = $(this).data('order-number');

        if (confirm(`Xác nhận thanh toán cho đơn hàng ${orderNumber}?`)) {
            $.ajax({
                url: `/admin/orders/${orderId}/confirm-payment`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                },
                success: function(response) {
                    showToast('success', response.message || 'Xác nhận thanh toán thành công!');
                    location.reload(); // Tải lại trang để cập nhật
                },
                error: function() {
                    showToast('error', 'Có lỗi xảy ra khi xác nhận thanh toán!');
                }
            });
        }
    });

    // Toast notification function
    function showToast(type, message) {
        const toast = $(`
            <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-${type === 'success' ? 'check' : 'exclamation-triangle'}"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `);
        if (!$('.toast-container').length) {
            $('body').append('<div class="toast-container position-fixed top-0 end-0 p-3"></div>');
        }
        $('.toast-container').append(toast);
        toast.toast('show');
        setTimeout(() => toast.remove(), 5000);
    }
});
</script>
@endsection
