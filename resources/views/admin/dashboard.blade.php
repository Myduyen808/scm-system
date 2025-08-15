@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4">
                <i class="fas fa-tachometer-alt"></i> Admin Dashboard
            </h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <i class="fas fa-warehouse fa-3x text-primary mb-3"></i>
                    <h5 class="card-title">Quản lý kho</h5>
                    <p class="card-text">Thêm, sửa, xóa sản phẩm và cập nhật tồn kho</p>
                    <a href="{{ route('admin.inventory') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-right"></i> Vào kho hàng
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card border-success">
                <div class="card-body text-center">
                    <i class="fas fa-shopping-cart fa-3x text-success mb-3"></i>
                    <h5 class="card-title">Quản lý đơn hàng</h5>
                    <p class="card-text">Duyệt, cập nhật hoặc hủy đơn hàng</p>
                    <a href="{{ route('admin.orders') }}" class="btn btn-success">
                        <i class="fas fa-arrow-right"></i> Xem đơn hàng
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card border-info">
                <div class="card-body text-center">
                    <i class="fas fa-chart-bar fa-3x text-info mb-3"></i>
                    <h5 class="card-title">Báo cáo doanh thu</h5>
                    <p class="card-text">Xem thống kê sản phẩm bán chạy</p>
                    <a href="{{ route('admin.reports') }}" class="btn btn-info">
                        <i class="fas fa-arrow-right"></i> Xem báo cáo
                    </a>
                </div>
            </div>
        </div>

        <!-- Thêm Card mới cho Thêm Sản Phẩm -->
        <div class="col-md-4 mb-4">
            <div class="card border-secondary">
                <div class="card-body text-center">
                    <i class="fas fa-plus fa-3x text-secondary mb-3"></i>
                    <h5 class="card-title">Thêm Sản Phẩm</h5>
                    <p class="card-text">Thêm sản phẩm mới vào kho</p>
                    <a href="{{ route('admin.inventory.create') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right"></i> Thêm ngay
                    </a>
                </div>
            </div>
        </div>

        <!-- Thêm Card mới cho Quản Lý Người Dùng -->
        <div class="col-md-4 mb-4">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-3x text-warning mb-3"></i>
                    <h5 class="card-title">Quản Lý Người Dùng</h5>
                    <p class="card-text">Thêm, sửa, xóa và quản lý vai trò người dùng</p>
                    <a href="{{ route('admin.users') }}" class="btn btn-warning">
                        <i class="fas fa-arrow-right"></i> Quản lý người dùng
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>150</h4>
                            <p class="mb-0">Sản phẩm</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-box fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>25</h4>
                            <p class="mb-0">Đơn hàng mới</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-shopping-cart fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>5</h4>
                            <p class="mb-0">Sản phẩm sắp hết</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>₫50M</h4>
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
