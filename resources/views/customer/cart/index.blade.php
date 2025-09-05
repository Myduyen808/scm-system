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
                        <tr id="cart-item-{{ $item->id }}">
                            <td>
                                @if($item->product->image)
                                    <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" style="width: 50px; height: auto;">
                                @else
                                    <span>Không có hình ảnh</span>
                                @endif
                            </td>
                            <td>{{ $item->product->name }}</td>
                            <td>{{ number_format($item->product->current_price ?? $item->product->regular_price ?? 0, 0, ',', '.') }} đ</td>
                            <td>
                                <form id="update-form-{{ $item->id }}" data-item-id="{{ $item->id }}">
                                    @csrf
                                    <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->product->inventory ? $item->product->inventory->stock : 0 }}" class="form-control d-inline w-50 quantity-input" data-price="{{ $item->product->current_price ?? $item->product->regular_price ?? 0 }}">
                                    <button type="button" class="btn btn-sm btn-primary update-quantity" data-item-id="{{ $item->id }}">Cập nhật</button>
                                </form>
                            </td>
                            <td id="total-{{ $item->id }}">{{ number_format(($item->product->current_price ?? $item->product->regular_price ?? 0) * $item->quantity, 0, ',', '.') }} đ</td>
                            <td>
                                <form action="{{ route('cart.remove', $item->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');" style="display:inline;">
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

        <!-- Nút Thanh toán -->
        <form action="{{ route('customer.checkout.store') }}" method="POST" class="mt-3">
            @csrf
            <div class="form-group">
                <label for="address_id">Chọn địa chỉ giao hàng:</label>
                @if (Auth::user()->addresses->isEmpty())
                    <p>Chưa có địa chỉ giao hàng! <a href="{{ route('customer.addresses.create') }}">Thêm địa chỉ mới</a></p>
                @else
                    <select name="address_id" id="address_id" class="form-control" required>
                        @foreach (Auth::user()->addresses as $address)
                            <option value="{{ $address->id }}" {{ $address->is_default ? 'selected' : '' }}>
                                {{ $address->name }} ({{ $address->phone }}) - {{ $address->address_line }}
                            </option>
                        @endforeach
                    </select>
                @endif
            </div>
            @if (!Auth::user()->addresses->isEmpty())
                <button type="submit" class="btn btn-success mt-3" id="checkout-btn">Thanh toán</button>
            @endif
        </form>

        <!-- JavaScript -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            $(document).ready(function() {
                // Cập nhật số lượng bằng AJAX
                $('.update-quantity').click(function() {
                    var itemId = $(this).data('item-id');
                    var quantity = $('#update-form-' + itemId + ' input[name="quantity"]').val();
                    var maxStock = parseInt($('#update-form-' + itemId + ' input[name="quantity"]').attr('max'));
                    var price = parseFloat($('#update-form-' + itemId + ' input[name="quantity"]').data('price'));

                    if (quantity < 1 || quantity > maxStock) {
                        alert('Số lượng phải từ 1 đến ' + maxStock + '!');
                        return;
                    }

                    $.ajax({
                        url: '{{ route("cart.update", ":id") }}'.replace(':id', itemId),
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            quantity: quantity
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#total-' + itemId).text((response.newTotal).toLocaleString('vi-VN') + ' đ');
                                alert(response.message);
                                // Tải lại trang để cập nhật tổng (có thể tối ưu bằng cách tính lại tổng động)
                                location.reload();
                            } else {
                                alert('Cập nhật thất bại: ' + response.message);
                            }
                        },
                        error: function(xhr) {
                            console.log(xhr.responseText); // Debug lỗi
                            alert('Đã xảy ra lỗi khi cập nhật: ' + (xhr.responseJSON ? xhr.responseJSON.message : 'Lỗi không xác định'));
                        }
                    });
                });

                // Đồng bộ giá trị select với form checkout
                $('#address_id').on('change', function() {
                    $('#checkout_address_id').val($(this).val());
                    $('#checkout-btn').prop('disabled', !$(this).val());
                });
            });
        </script>
    @endif
</div>
@endsection
