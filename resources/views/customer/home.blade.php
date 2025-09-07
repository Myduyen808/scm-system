@extends('layouts.app')

@section('title', 'Customer Home')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4">
                <i class="fas fa-home"></i> Chào mừng khách hàng
            </h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <i class="fas fa-shopping-bag fa-3x text-primary mb-3"></i>
                    <h5 class="card-title">Xem sản phẩm</h5>
                    <p class="card-text">Duyệt các sản phẩm có sẵn</p>
                    <a href="{{ route('customer.products') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-right"></i> Xem sản phẩm
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card border-success">
                <div class="card-body text-center">
                    <i class="fas fa-shopping-cart fa-3x text-success mb-3"></i>
                    <h5 class="card-title">Giỏ hàng</h5>
                    <p class="card-text">Xem và quản lý giỏ hàng</p>
                    <a href="{{ route('customer.cart') }}" class="btn btn-success">
                        <i class="fas fa-arrow-right"></i> Xem giỏ hàng
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card border-info">
                <div class="card-body text-center">
                    <i class="fas fa-list-alt fa-3x text-info mb-3"></i>
                    <h5 class="card-title">Đơn hàng của tôi</h5>
                    <p class="card-text">Theo dõi trạng thái đơn hàng</p>
                    <a href="{{ route('customer.orders') }}" class="btn btn-info">
                        <i class="fas fa-arrow-right"></i> Xem đơn hàng
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <i class="fas fa-star fa-3x text-warning mb-3"></i>
                    <h5 class="card-title">Đánh giá</h5>
                    <p class="card-text">Đánh giá sản phẩm đã mua</p>
                    <a href="{{ route('customer.products.for-review') }}" class="btn btn-warning">
                        <i class="fas fa-arrow-right"></i> Xem sản phẩm để đánh giá
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card border-secondary">
                <div class="card-body text-center">
                    <i class="fas fa-map-marker-alt fa-3x text-secondary mb-3"></i>
                    <h5 class="card-title">Địa chỉ giao hàng</h5>
                    <p class="card-text">Quản lý địa chỉ của bạn</p>
                    <a href="{{ route('customer.addresses.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right"></i> Xem địa chỉ
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card border-dark">
                <div class="card-body text-center">
                    <i class="fas fa-truck fa-3x text-dark mb-3"></i>
                    <h5 class="card-title">Theo dõi giao hàng</h5>
                    <p class="card-text">Kiểm tra trạng thái giao hàng</p>
                    <a href="{{ route('customer.orders') }}" class="btn btn-dark">
                        <i class="fas fa-arrow-right"></i> Xem đơn hàng để theo dõi
                    </a>
                </div>
            </div>
        </div>

        @can('create tickets')
        <div class="col-md-3 mb-4">
            <div class="card border-danger">
                <div class="card-body text-center">
                    <i class="fas fa-headset fa-3x text-danger mb-3"></i>
                    <h5 class="card-title">Hỗ trợ khách hàng</h5>
                    <p class="card-text">Tạo và xem yêu cầu hỗ trợ</p>
                    <a href="{{ route('customer.support') }}" class="btn btn-danger">
                        <i class="fas fa-arrow-right"></i> Hỗ trợ
                    </a>
                </div>
            </div>
        </div>
        @endcan
    </div>

<!-- Featured Products Section -->
<div class="row mt-5">
    <div class="col-md-12">
        <h2 class="mb-4">Sản phẩm nổi bật</h2>
    </div>
</div>

<div class="row">
    @forelse($featuredProducts as $product)
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
                    @if($product->current_price && $product->current_price < ($product->sale_price ?? $product->regular_price))
                        <del class="text-muted">₫{{ number_format($product->regular_price, 0, ',', '.') }}</del>
                        <strong class="text-danger">₫{{ number_format($product->current_price, 0, ',', '.') }}</strong>
                    @elseif($product->sale_price && $product->sale_price < $product->regular_price)
                        <del class="text-muted">₫{{ number_format($product->regular_price, 0, ',', '.') }}</del>
                        <strong class="text-danger">₫{{ number_format($product->sale_price, 0, ',', '.') }}</strong>
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
                <!-- Nút Yêu thích -->
                <button class="btn btn-outline-danger btn-sm mt-2 favorite-btn {{ auth()->check() && auth()->user()->favorites->contains($product->id) ? 'active' : '' }}"
                        data-product-id="{{ $product->id }}"
                        title="Yêu thích">
                    <svg class="icon_heart"><use href="#icon_heart"></use></svg>
                </button>
                <!-- Nút Viết đánh giá (chỉ hiển thị nếu đã đặt sản phẩm) -->
                @if(auth()->check() && auth()->user()->orders()->whereHas('orderItems', function($query) use ($product) {
                    $query->where('product_id', $product->id);
                })->exists())
                    <a href="{{ route('customer.reviews.create', $product->id) }}" class="btn btn-warning btn-sm mt-2">
                        <i class="fas fa-star"></i> Viết đánh giá
                    </a>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="col-md-12"><p class="text-center">Không có sản phẩm nổi bật.</p></div>
    @endforelse
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
        width: 20px;
        height: 20px;
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
    .card-img-top {
    width: 100%;       /* chiếm toàn bộ chiều ngang card */
    max-height: 250px; /* giới hạn chiều cao tối đa */
    object-fit: cover; /* giữ tỉ lệ, cắt bớt nếu cần */
    border-radius: 5px;
}
</style>
@endsection
