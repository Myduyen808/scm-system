@extends('layouts.app')

@section('title', 'Giỏ Hàng - Khách Hàng')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 text-center mb-4">
            <h1 class="display-5 fw-bold text-primary mt-3"><i class="fas fa-shopping-cart"></i> Giỏ hàng của bạn</h1>
            <p class="text-muted">Hệ thống quản lý chuỗi cung ứng hiện đại và hiệu quả</p>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm">
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

                    @if ($cartItems->isEmpty())
                        <p class="text-center">Giỏ hàng trống!</p>
                        <a href="{{ route('customer.products') }}" class="btn btn-primary mt-3">Mua sắm ngay</a>
                    @else
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>Giá</th>
                                    <th>Số lượng</th>
                                    <th>Tổng</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cartItems as $item)
                                    @if ($item->product)
                                        <tr>
                                            <td>{{ $item->product->name }}</td>
                                            <td>{{ number_format($item->product->current_price, 0, ',', '.') }} đ</td>
                                            <td>
                                                <form action="{{ route('cart.update', $item->id) }}" method="POST">
                                                    @csrf
                                                    <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" class="form-control d-inline-block w-auto">
                                                    <button type="submit" class="btn btn-sm btn-primary mt-2">Cập nhật</button>
                                                </form>
                                            </td>
                                            <td>{{ number_format($item->product->current_price * $item->quantity, 0, ',', '.') }} đ</td>
                                            <td>
                                                <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td colspan="5">Sản phẩm không tồn tại hoặc đã bị xóa.</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                        <p class="fw-bold">Tổng tiền: {{ number_format($total, 0, ',', '.') }} đ</p>
                        @if ($discountedTotal < $total)
                            <p class="fw-bold">Tổng sau giảm giá: {{ number_format($discountedTotal, 0, ',', '.') }} đ</p>
                        @endif

                        <a href="{{ route('customer.checkout') }}" class="btn btn-success btn-lg w-100 mt-3">
                            <i class="fas fa-credit-card"></i> Tiến hành thanh toán
                        </a>
                        <a href="{{ route('customer.products') }}" class="btn btn-outline-primary mt-3 w-100">
                            <i class="fas fa-arrow-left"></i> Tiếp tục mua sắm
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
