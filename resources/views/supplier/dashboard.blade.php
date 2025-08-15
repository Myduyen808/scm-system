@extends('layouts.app')

@section('title', 'Supplier Dashboard')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4">
                <i class="fas fa-truck"></i> Supplier Dashboard
            </h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <i class="fas fa-boxes fa-3x text-primary mb-3"></i>
                    <h5 class="card-title">Quản lý sản phẩm</h5>
                    <p class="card-text">Cập nhật danh sách sản phẩm, giá, số lượng</p>
                    <a href="{{ route('supplier.products') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-right"></i> Quản lý sản phẩm
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card border-success">
                <div class="card-body text-center">
                    <i class="fas fa-clipboard-list fa-3x text-success mb-3"></i>
                    <h5 class="card-title">Theo dõi đơn hàng</h5>
                    <p class="card-text">Xem đơn hàng liên quan đến sản phẩm</p>
                    <a href="{{ route('supplier.orders') }}" class="btn btn-success">
                        <i class="fas fa-arrow-right"></i> Xem đơn hàng
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card border-info">
                <div class="card-body text-center">
                    <i class="fas fa-handshake fa-3x text-info mb-3"></i>
                    <h5 class="card-title">Phản hồi yêu cầu</h5>
                    <p class="card-text">Xử lý yêu cầu nhập hàng từ hệ thống</p>
                    <a href="{{ route('supplier.requests') }}" class="btn btn-info">
                        <i class="fas fa-arrow-right"></i> Xử lý yêu cầu
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Supplier Statistics -->
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>45</h4>
                            <p class="mb-0">Sản phẩm cung cấp</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-box fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>12</h4>
                            <p class="mb-0">Đơn hàng chờ xử lý</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>₫25M</h4>
                            <p class="mb-0">Doanh thu tháng</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-dollar-sign fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
