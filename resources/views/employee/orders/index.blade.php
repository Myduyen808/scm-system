@extends('layouts.app')

@section('title', 'Xử Lý Đơn Hàng - Nhân Viên')

@section('content')
<div class="container">
    <h1><i class="fas fa-tasks"></i> Xử Lý Đơn Hàng</h1>
    <form method="GET" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Tìm mã đơn hàng..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary">Tìm</button>
        </div>
    </form>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Hình ảnh</th>
                <th>Mã đơn hàng</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
            <tr>
                <td>
                    @if($order->orderItems->isNotEmpty())
                        @if($order->orderItems->first()->product && $order->orderItems->first()->product->image)
                            <img src="{{ Storage::url($order->orderItems->first()->product->image) }}" alt="{{ $order->orderItems->first()->product->name }}" class="img-thumbnail" style="max-width: 50px; max-height: 50px;">
                        @else
                            <img src="{{ asset('images/placeholder.jpg') }}" alt="No image" class="img-thumbnail" style="max-width: 50px; max-height: 50px;">
                        @endif
                    @else
                        <img src="{{ asset('images/placeholder.jpg') }}" alt="No image" class="img-thumbnail" style="max-width: 50px; max-height: 50px;">
                    @endif
                </td>
                <td>{{ $order->order_number }}</td>
                <td>
                    <form action="{{ route('employee.orders.update-status', $order) }}" method="POST" class="d-inline">
                        @csrf @method('PATCH')
                        <select name="status" class="form-control d-inline w-50" required>
                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                            <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                            <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Đã giao</option>
                            <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Hoàn thành</option>
                            <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                        </select>
                        <button type="submit" class="btn btn-sm btn-success">Cập nhật</button>
                    </form>
                </td>
                <td>
                    <a href="{{ route('employee.orders.show', $order) }}" class="btn btn-info btn-sm">Chi tiết</a>
                    <form action="{{ route('employee.orders.cancel', $order) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn hủy đơn hàng?');" class="d-inline">
                        @csrf @method('POST')
                        <button type="submit" class="btn btn-danger btn-sm">Hủy</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center">Không có đơn hàng</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    {{ $orders->links() }}
</div>
@endsection
