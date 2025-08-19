@extends('layouts.app')

@section('title', 'Giỏ Hàng - Khách Hàng')

@section('content')
<div class="container">
    <h1 class="mb-4"><i class="fas fa-shopping-cart"></i> Giỏ hàng của bạn</h1>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($cartItems->isEmpty())
        <p>Giỏ hàng trống!</p>
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
                                <form action="{{ route('customer.cart.update', $item->product_id) }}" method="POST">
                                    @csrf
                                    <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" class="form-control d-inline-block w-auto">
                                    <button type="submit" class="btn btn-sm btn-primary mt-2">Cập nhật</button>
                                </form>
                            </td>
                            <td>{{ number_format($item->product->current_price * $item->quantity, 0, ',', '.') }} đ</td>
                            <td>
                                <form action="{{ route('customer.cart.remove', $item->product_id) }}" method="POST">
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
        <p><strong>Tổng tiền: {{ number_format($total, 0, ',', '.') }} đ</strong></p>
        @if ($discountedTotal < $total)
            <p><strong>Tổng sau giảm giá: {{ number_format($discountedTotal, 0, ',', '.') }} đ</strong></p>
        @endif

        <form action="{{ route('customer.checkout.store') }}" method="POST" class="mt-3">
            @csrf
            <div class="form-group">
                <label for="address_id">Chọn địa chỉ giao hàng:</label>
                @if (Auth::user()->addresses->isEmpty())
                    <p>Chưa có địa chỉ giao hàng! <a href="{{ route('customer.addresses.create') }}">Thêm địa chỉ mới</a></p>
                @else
                    <select name="address_id" id="address_id" class="form-control" required>
                        @foreach (Auth::user()->addresses as $address)
                            <option value="{{ $address->id }}">{{ $address->name }} - {{ $address->address_line }}</option>
                        @endforeach
                    </select>
                @endif
            </div>
            @if (!Auth::user()->addresses->isEmpty())
                <button type="submit" class="btn btn-success mt-3">Thanh toán</button>
            @endif
        </form>
    @endif
</div>
@endsection
