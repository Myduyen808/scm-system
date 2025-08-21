@extends('layouts.app')

@section('title', 'Danh Sách Sản Phẩm - Khách Hàng')

@section('content')
<div class="container">
    <h1 class="mb-4"><i class="fas fa-shopping-bag"></i> Danh Sách Sản Phẩm</h1>
    <div class="row mb-4">
        <div class="col-md-6">
            <form method="GET" class="d-flex">
                <input type="text" name="search" placeholder="Tìm tên hoặc SKU..." value="{{ request()->input('search') }}" class="form-control me-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Tìm</button>
            </form>
        </div>
    </div>
    <div class="row">
        @forelse($products as $product)
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
                <div class="card-body">
                    <a href="{{ route('customer.products.show', $product->id) }}" class="text-decoration-none text-dark">
                        <h6 class="card-title">{{ $product->name }}</h6>
                    </a>
                    <p class="card-text">
                        @if($product->regular_price && $product->current_price < $product->regular_price)
                            <del class="text-muted">₫{{ number_format($product->regular_price, 0, ',', '.') }}</del>
                            <strong class="text-danger">₫{{ number_format($product->current_price, 0, ',', '.') }}</strong>
                        @else
                            <strong>₫{{ number_format($product->current_price, 0, ',', '.') }}</strong>
                        @endif
                    </p>
                    <form action="{{ route('customer.cart.add', $product->id) }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="btn btn-primary btn-sm add-to-cart-btn">
                            <i class="fas fa-cart-plus"></i> Thêm vào giỏ
                        </button>
                    </form>
                    <a href="{{ route('customer.reviews.create', $product->id) }}" class="btn btn-warning btn-sm mt-2">
                        <i class="fas fa-star"></i> Viết đánh giá
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-md-12"><p class="text-center">Không có sản phẩm nào.</p></div>
        @endforelse
    </div>
    {{ $products->links() }}
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
