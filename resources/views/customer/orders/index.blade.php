@extends('layouts.app')

@section('title', 'Đơn Hàng Của Tôi - Khách Hàng')

@section('content')
<div class="container">
    <h1 class="mb-4"><i class="fas fa-list-alt"></i> Đơn Hàng Của Tôi</h1>
    <div class="card fade-in">
        <div class="card-body">
            <form method="GET" class="mb-4 d-flex">
                <input type="text" name="search" placeholder="Tìm mã đơn hàng..." value="{{ request()->input('search') }}" class="form-control me-2">
                <select name="status" class="form-control w-25 me-2">
                    <option value="">Tất cả trạng thái</option>
                    <option value="pending" {{ request()->input('status') == 'pending' ? 'selected' : '' }}>Đang chờ</option>
                    <option value="processing" {{ request()->input('status') == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                    <option value="completed" {{ request()->input('status') == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                    <option value="cancelled" {{ request()->input('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                </select>
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Tìm</button>
            </form>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Mã đơn hàng</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Ngày đặt</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td>{{ $order->order_number }}</td>
                        <td>₫{{ number_format($order->total_amount, 0, ',', '.') }}</td>
                        <td>{{ $order->status }}</td>
                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <a href="{{ route('customer.orders.show', $order->id) }}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i> Xem</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center">Chưa có đơn hàng.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection
