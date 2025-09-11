@extends('layouts.app')

@section('title', 'Thêm Sản Phẩm - Nhà Cung Cấp')

@section('content')
<div class="container">
    <h1 class="mb-4"><i class="fas fa-plus"></i> Thêm Sản Phẩm Mới</h1>
    <!-- Flash Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="card fade-in">
        <div class="card-body">
            <form action="{{ route('supplier.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Mô tả</label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Giá thường (VND) <span class="text-danger">*</span></label>
                    <input type="number" name="regular_price" class="form-control" value="{{ old('regular_price', $product->regular_price ?? '') }}" required min="0" step="1">
                </div>

                <div class="mb-3">
                    <label class="form-label">Giá sale (VND) <span class="text-muted">(Tùy chọn, không vượt quá giá thường)</span></label>
                    <input type="number" name="sale_price" class="form-control" value="{{ old('sale_price', $product->sale_price ?? '') }}" min="0" step="1">
                </div>
                <div class="mb-3">
                    <label class="form-label">Sale (%) <span class="text-muted">(Tùy chọn, 0-100)</span></label>
                    <input type="number" name="sale_percent" id="sale_percent" class="form-control" value="{{ old('sale_percent', $product->sale_percent ?? 0) }}" min="0" max="100" step="0.1">
                </div>
                <div class="mb-3">
                    <label class="form-label">SKU <span class="text-danger">*</span></label>
                    <input type="text" name="sku" class="form-control @error('sku') is-invalid @enderror" value="{{ old('sku') }}" required>
                    @error('sku') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Tồn kho ban đầu <span class="text-danger">*</span></label>
                    <input type="number" name="stock_quantity" class="form-control @error('stock_quantity') is-invalid @enderror" value="{{ old('stock_quantity') }}" required min="0">
                    @error('stock_quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Mùa vụ (season)</label>
                        <select name="season" class="form-control @error('season') is-invalid @enderror">
                            <option value="">-- Chọn mùa --</option>
                            <option value="spring" {{ old('season') == 'spring' ? 'selected' : '' }}>Mùa xuân</option>
                            <option value="summer" {{ old('season') == 'summer' ? 'selected' : '' }}>Mùa hè</option>
                            <option value="autumn" {{ old('season') == 'autumn' ? 'selected' : '' }}>Mùa thu</option>
                            <option value="winter" {{ old('season') == 'winter' ? 'selected' : '' }}>Mùa đông</option>
                        </select>
                        @error('season') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Hình ảnh</label>
                    <input type="file" name="image" class="form-control @error('image') is-invalid @enderror">
                    @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Lưu</button>
                <a href="{{ route('supplier.products') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Hủy</a>
            </form>
        </div>
    </div>
</div>
@endsection
<script>
document.addEventListener('DOMContentLoaded', function() {
    const regularPriceInput = document.querySelector('input[name="regular_price"]');
    const salePriceInput = document.querySelector('input[name="sale_price"]');
    const salePercentInput = document.querySelector('input[name="sale_percent"]');

    function updateSalePrice() {
        const regularPrice = parseFloat(regularPriceInput.value) || 0;
        const percent = parseFloat(salePercentInput.value) || 0;

        // Nếu nhập % thì tính giá sale
        if(percent > 0 && percent <= 100) {
            salePriceInput.value = (regularPrice * (1 - percent / 100)).toFixed(2);
        }
    }

    salePercentInput.addEventListener('input', updateSalePrice);
    regularPriceInput.addEventListener('input', updateSalePrice);
});
</script>

