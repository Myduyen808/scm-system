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
                    <a href="#" class="btn btn-warning">
                        <i class="fas fa-arrow-right"></i> Đánh giá
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Featured Products Section -->
    <div class="row mt-5">
        <div class="col-md-12">
            <h2 class="mb-4">Sản phẩm nổi bật</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card">
                <img src="https://via.placeholder.com/250x200" class="card-img-top" alt="Product">
                <div class="card-body">
                    <h6 class="card-title">Sản phẩm 1</h6>
                    <p class="card-text">
                        <del class="text-muted">₫500,000</del>
                        <strong class="text-danger">₫450,000</strong>
                    </p>
                    <button class="btn btn-primary btn-sm">
                        <i class="fas fa-cart-plus"></i> Thêm vào giỏ
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card">
                <img src="https://via.placeholder.com/250x200" class="card-img-top" alt="Product">
                <div class="card-body">
                    <h6 class="card-title">Sản phẩm 2</h6>
                    <p class="card-text">
                        <del class="text-muted">₫300,000</del>
                        <strong class="text-danger">₫250,000</strong>
                    </p>
                    <button class="btn btn-primary btn-sm">
                        <i class="fas fa-cart-plus"></i> Thêm vào giỏ
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card">
                <img src="https://via.placeholder.com/250x200" class="card-img-top" alt="Product">
                <div class="card-body">
                    <h6 class="card-title">Sản phẩm 3</h6>
                    <p class="card-text">
                        <del class="text-muted">₫800,000</del>
                        <strong class="text-danger">₫720,000</strong>
                    </p>
                    <button class="btn btn-primary btn-sm">
                        <i class="fas fa-cart-plus"></i> Thêm vào giỏ
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card">
                <img src="https://via.placeholder.com/250x200" class="card-img-top" alt="Product">
                <div class="card-body">
                    <h6 class="card-title">Sản phẩm 4</h6>
                    <p class="card-text">
                        <del class="text-muted">₫1,200,000</del>
                        <strong class="text-danger">₫1,000,000</strong>
                    </p>
                    <button class="btn btn-primary btn-sm">
                        <i class="fas fa-cart-plus"></i> Thêm vào giỏ
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
