@extends('layouts.app')

@section('title', 'Tạo Yêu Cầu Nhập Hàng - Nhân Viên')

@section('content')
<div class="container">
    <h1 class="mb-4"><i class="fas fa-plus"></i> Tạo Yêu Cầu Nhập Hàng</h1>
    <div class="card fade-in">
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('employee.sendStockRequest') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="product_id" class="form-label">Sản phẩm cần nhập</label>
                    <select name="product_id" class="form-control" id="product_id" required>
                        <option value="">Chọn sản phẩm</option>
                        @foreach ($lowStockProducts as $product)
                            <option value="{{ $product->id }}" data-supplier-id="{{ $product->supplier_id ?? '' }}"
                                    {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->name }} (Tồn: {{ $product->stock_quantity }}, Nhà cung cấp: {{ $product->supplier->name ?? 'Chưa rõ' }})
                            </option>
                        @endforeach
                    </select>
                    @error('product_id')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="quantity" class="form-label">Số lượng nhập</label>
                    <input type="number" name="quantity" class="form-control" id="quantity" min="1" value="{{ old('quantity') }}" required>
                    @error('quantity')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="supplier_id" class="form-label">Nhà cung cấp</label>
                    <select name="supplier_id" class="form-control" id="supplier_id" required>
                        <option value="">Chọn nhà cung cấp</option>
                        @if(isset($suppliers))
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('supplier_id')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="note" class="form-label">Ghi chú</label>
                    <textarea name="note" class="form-control" id="note" rows="3">{{ old('note') }}</textarea>
                    @error('note')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Gửi yêu cầu</button>
                <a href="{{ route('employee.requests') }}" class="btn btn-secondary">Quay lại</a>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#product_id').on('change', function() {
        const supplierId = $(this).find(':selected').data('supplier-id');
        if (supplierId) {
            $('#supplier_id').val(supplierId);
        }
        $('#supplier_id').trigger('change');
    }).trigger('change');

    $('#supplier_id').on('change', function() {
        const selectedProductSupplierId = $('#product_id').find(':selected').data('supplier-id');
        if (selectedProductSupplierId && $(this).val() !== selectedProductSupplierId) {
            alert('Nhà cung cấp không khớp với sản phẩm. Vui lòng chọn nhà cung cấp đúng.');
            $(this).val(selectedProductSupplierId);
        }
    });
});
</script>
@endpush
