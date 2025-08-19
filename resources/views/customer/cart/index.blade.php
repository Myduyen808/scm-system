@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Giỏ hàng của bạn</h1>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($cartItems->isEmpty())
        <p>Giỏ hàng trống!</p>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>Hình ảnh</th>
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
                            <td>
                                @if($item->product->image)
                                    <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" style="width: 50px; height: auto;">
                                @else
                                    <span>Không có hình ảnh</span>
                                @endif
                            </td>
                            <td>{{ $item->product->name }}</td>
                            <td>{{ number_format($item->product->current_price, 0, ',', '.') }} đ</td>
                            <td>
                                <form action="{{ route('cart.update', $item->id) }}" method="POST">
                                    @csrf
                                    <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" class="form-control d-inline w-50">
                                    <button type="submit" class="btn btn-sm btn-primary">Cập nhật</button>
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
                            <td colspan="6">Sản phẩm không tồn tại hoặc đã bị xóa.</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
        <p><strong>Tổng tiền: {{ number_format($total, 0, ',', '.') }} đ</strong></p>
        @if ($discountedTotal < $total)
            <p><strong>Tổng sau giảm giá: {{ number_format($discountedTotal, 0, ',', '.') }} đ</strong></p>
        @endif

        <!-- Form Checkout -->
        <div class="form-group">
            <label for="address_id">Chọn địa chỉ giao hàng:</label>
            <select name="address_id" id="address_id" class="form-control" required>
                @forelse (Auth::user()->addresses as $address)
                    <option value="{{ $address->id }}">{{ $address->name }} - {{ $address->address_line }}</option>
                @empty
                    <option value="" disabled>Chưa có địa chỉ. Vui lòng thêm địa chỉ!</option>
                @endforelse
            </select>
            @if (Auth::user()->addresses->isEmpty())
                <a href="{{ route('customer.addresses.create') }}" class="btn btn-link mt-2">Thêm địa chỉ mới</a>
            @endif
        </div>

        <!-- Nút Thanh toán -->
        <form action="{{ route('customer.checkout.store') }}" method="POST" class="mt-3">
            @csrf
            <input type="hidden" name="address_id" id="checkout_address_id" value="">
            <button type="submit" class="btn btn-success" id="checkout-btn" @if (Auth::user()->addresses->isEmpty()) disabled @endif>Thanh toán</button>
        </form>

        <!-- JavaScript để đồng bộ giá trị select -->
        <script>
            document.getElementById('address_id').addEventListener('change', function() {
                document.getElementById('checkout_address_id').value = this.value;
                document.getElementById('checkout-btn').disabled = !this.value;
            });
        </script>
    @endif
</div>
@endsection
