@extends('layouts.app')

@section('title', 'Chi Tiết Sản Phẩm - {{ $product->name }}')

@section('content')
<div class="container py-5">
    <div class="row">
        <!-- Hình ảnh sản phẩm -->
        <div class="col-md-6">
            <div class="card shadow-sm position-relative">
                @if($product->regular_price && $product->current_price < $product->regular_price)
                    <?php
                        $discountPercentage = round((($product->regular_price - $product->current_price) / $product->regular_price) * 100);
                    ?>
                    <span class="sale-badge">Giảm {{ $discountPercentage }}%</span>
                @endif
                <img src="{{ $product->image ? Storage::url($product->image) : 'https://via.placeholder.com/400x300' }}"
                     alt="{{ $product->name }}" class="card-img-top img-fluid rounded"
                     style="max-height: 400px; object-fit: contain; width: 100%;">
            </div>
        </div>

        <!-- Thông tin sản phẩm -->
        <div class="col-md-6">
            <h2 class="mb-3">{{ $product->name }}</h2>
            <p class="text-muted mb-4" style="font-size: 1.1rem;">{{ $product->description ?: 'Chưa có mô tả.' }}</p>

            <div class="mb-4">
                <h4 class="text-danger">
                    Giá: <strong>₫{{ number_format($product->current_price ?? $product->regular_price ?? 0, 0, ',', '.') }}</strong>
                    @if($product->regular_price && $product->current_price < $product->regular_price)
                        <span class="text-muted text-decoration-line-through">₫{{ number_format($product->regular_price, 0, ',', '.') }}</span>
                    @endif
                </h4>
            </div>

            <div class="mb-4">
                <p><strong>Tồn kho:</strong> {{ $product->inventory ? $product->inventory->stock : 0 }} sản phẩm</p>
            </div>

            <!-- Form thêm vào giỏ -->
            @if($product->inventory && $product->inventory->stock > 0 && $product->is_approved && $product->is_active)
                <form action="{{ route('customer.cart.add', $product->id) }}" method="POST" class="mb-4">
                    @csrf
                    <div class="input-group mb-3" style="max-width: 200px;">
                        <input type="number" name="quantity" value="1" min="1"
                               max="{{ $product->inventory->stock }}"
                               class="form-control" style="padding: 8px;" required>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-cart-plus"></i> Thêm vào giỏ
                        </button>
                    </div>
                </form>
            @else
                <p class="text-danger">Sản phẩm hiện không khả dụng để thêm vào giỏ hàng.</p>
            @endif

            <!-- Nút viết đánh giá -->
            <a href="{{ route('customer.reviews.create', $product->id) }}" class="btn btn-warning">
                <i class="fas fa-star"></i> Viết đánh giá
            </a>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .sale-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        background-color: #ff4444;
        color: white;
        padding: 5px 10px;
        border-radius: 5px;
        font-weight: bold;
        font-size: 14px;
        transform: rotate(-20deg);
        z-index: 1;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .card {
        transition: transform 0.3s ease;
    }

    .card:hover {
        transform: scale(1.05);
    }
</style>
@endsection
