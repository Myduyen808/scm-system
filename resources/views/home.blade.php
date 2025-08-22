@extends('layouts.app')

@section('title', 'Trang Chủ - SCM System')

@section('content')
<!-- Hero Section -->
<section class="hero-section bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 fade-in">
                <h1 class="display-4 fw-bold mb-4">Hệ Thống Quản Lý Chuỗi Cung Ứng</h1>
                <p class="lead mb-4">Giải pháp toàn diện cho việc quản lý kho hàng, đơn hàng và thanh toán một cách hiệu quả và thông minh.</p>
                <div class="d-flex gap-3">
                    <a href="{{ route('customer.products') }}" class="btn btn-light btn-lg">
                        <i class="fas fa-shopping-bag me-2"></i>Mua sắm ngay
                    </a>
                    <a href="#features" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-info-circle me-2"></i>Tìm hiểu thêm
                    </a>
                    @guest
                        <a href="{{ route('about') }}" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-users me-2"></i>Về chúng tôi
                        </a>
                    @endguest
                </div>
            </div>
            <div class="col-lg-6 fade-in">
                <div class="text-center">
                    <i class="fas fa-cube" style="font-size: 200px; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="py-5">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-12">
                <h2 class="fw-bold mb-3">Tính năng nổi bật</h2>
                <p class="lead text-muted">Những tính năng mạnh mẽ giúp tối ưu hóa chuỗi cung ứng của bạn</p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-4 fade-in">
                <div class="card h-100 text-center p-4">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fas fa-warehouse text-primary" style="font-size: 3rem;"></i>
                        </div>
                        <h5 class="card-title">Quản lý kho hàng</h5>
                        <p class="card-text text-muted">Theo dõi tồn kho realtime, quản lý nhập xuất hàng hóa một cách tự động và chính xác.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4 fade-in">
                <div class="card h-100 text-center p-4">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fas fa-shopping-cart text-primary" style="font-size: 3rem;"></i>
                        </div>
                        <h5 class="card-title">Đặt hàng dễ dàng</h5>
                        <p class="card-text text-muted">Giao diện thân thiện, giỏ hàng thông minh và quy trình checkout nhanh chóng.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4 fade-in">
                <div class="card h-100 text-center p-4">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fas fa-credit-card text-primary" style="font-size: 3rem;"></i>
                        </div>
                        <h5 class="card-title">Thanh toán an toàn</h5>
                        <p class="card-text text-muted">Tích hợp đa dạng phương thức thanh toán với bảo mật cao và xử lý nhanh chóng.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Products -->
@if(isset($featuredProducts) && $featuredProducts->count() > 0)
<section class="py-5 bg-light">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-12">
                <h2 class="fw-bold mb-3">Sản phẩm nổi bật</h2>
                <p class="lead text-muted">Những sản phẩm bán chạy và có ưu đãi đặc biệt</p>
            </div>
        </div>

        <div class="row">
            @foreach($featuredProducts as $product)
            <div class="col-lg-4 col-md-6 mb-4 fade-in">
                <div class="card position-relative">
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
                                <strong>₫{{ number_format($product->regular_price, 0, ',', '.') }}</strong>
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
            @endforeach
        </div>

        <div class="text-center">
            <a href="{{ route('customer.products') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-arrow-right me-2"></i>Xem tất cả sản phẩm
            </a>
        </div>
    </div>
</section>
@endif

<!-- Statistics -->
<section class="py-5">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3 mb-4 fade-in">
                <div class="card border-0 bg-primary text-white p-4">
                    <h3 class="fw-bold">{{ $productCount }}</h3>
                    <p class="mb-0">Sản phẩm</p>
                </div>
            </div>
            <div class="col-md-3 mb-4 fade-in">
                <div class="card border-0 bg-success text-white p-4">
                    <h3 class="fw-bold">{{ $orderCount }}</h3>
                    <p class="mb-0">Đơn hàng</p>
                </div>
            </div>
            <div class="col-md-3 mb-4 fade-in">
                <div class="card border-0 bg-info text-white p-4">
                    <h3 class="fw-bold">{{ $userCount }}</h3>
                    <p class="mb-0">Người dùng</p>
                </div>
            </div>
            <div class="col-md-3 mb-4 fade-in">
                <div class="card border-0 bg-warning text-white p-4">
                    <h3 class="fw-bold">{{ $totalStock }}</h3>
                    <p class="mb-0">Tổng tồn kho</p>
                </div>
            </div>
        </div>
    </div>
</section>
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
