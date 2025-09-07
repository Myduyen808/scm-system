@extends('layouts.app')

@section('title', 'Danh Sách Yêu Thích - Khách Hàng')

@section('content')
<div class="container">
    <h1 class="mb-4"><i class="fas fa-heart"></i> Danh Sách Yêu Thích</h1>
    <div class="row">
        @forelse($favorites as $product)
        <div class="col-md-3 mb-4">
            <div class="card fade-in position-relative">
                @if($product->regular_price && $product->current_price < $product->regular_price)
                    <?php
                        $discountPercentage = round((($product->regular_price - $product->current_price) / $product->regular_price) * 100);
                    ?>
                    <span class="sale-badge">Sale {{ $discountPercentage }}%</span>
                @endif
                <a href="{{ route('customer.products.show', $product->id) }}" class="text-decoration-none">
                    <img src="{{ $product->image ? Storage::url($product->image) : 'https://via.placeholder.com/250x200' }}" class="card-img-top" alt="{{ $product->name }}">
                </a>
                <div class="card-body d-flex flex-column">
                    <a href="{{ route('customer.products.show', $product->id) }}" class="text-decoration-none text-dark">
                        <h6 class="card-title">{{ $product->name }}</h6>
                    </a>
                    <p class="card-text mb-2">
                        @if($product->regular_price && $product->current_price < $product->regular_price)
                            <del class="text-muted">₫{{ number_format($product->regular_price, 0, ',', '.') }}</del>
                            <strong class="text-danger">₫{{ number_format($product->current_price, 0, ',', '.') }}</strong>
                        @else
                            <strong>₫{{ number_format($product->current_price, 0, ',', '.') }}</strong>
                        @endif
                    </p>
                    <div class="d-flex gap-2 mt-auto">
                        <!-- Nút Bỏ yêu thích -->
                        <button class="btn btn-outline-danger btn-sm favorite-btn {{ $product->pivot->user_id == Auth::id() ? 'active' : '' }}"
                                data-product-id="{{ $product->id }}"
                                title="Bỏ yêu thích">
                            <svg class="icon_heart" style="width: 16px; height: 16px;"><use href="#icon_heart"></use></svg>
                        </button>
                        <!-- Thêm vào giỏ -->
                        <form action="{{ route('customer.cart.add', $product->id) }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-cart-plus"></i> Thêm vào giỏ
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-md-12"><p class="text-center">Không có sản phẩm nào trong danh sách yêu thích.</p></div>
        @endforelse
    </div>
    {{ $favorites->links() }}
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

    .icon_heart {
        width: 16px;
        height: 16px;
    }

    .favorite-btn {
        color: #ff4444;
        transition: color 0.3s ease, transform 0.3s ease;
    }

    .favorite-btn.active {
        color: #ff0000;
        transform: scale(1.2);
    }

    .favorite-btn:hover {
        color: #ff6666;
        transform: scale(1.1);
    }
</style>
@endsection
