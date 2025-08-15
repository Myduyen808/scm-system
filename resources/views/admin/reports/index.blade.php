@extends('layouts.app')

@section('title', 'Báo Cáo Doanh Thu')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="mb-0">
                    <i class="fas fa-chart-bar text-primary"></i> Báo Cáo Tổng Quan
                </h1>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Tổng Doanh Thu</h5>
                </div>
                <div class="card-body">
                    <h3 class="text-success">₫{{ number_format($revenue, 0, ',', '.') }}</h3>
                    <p class="text-muted">Doanh thu từ các đơn hàng đã thanh toán</p>
                    <a href="{{ route('admin.reports.revenue') }}" class="btn btn-primary mt-2">Xem chi tiết</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Top 5 Sản Phẩm Bán Chạy</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        @forelse($topProducts as $product)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $product->name }}
                                <span class="badge bg-primary rounded-pill">{{ $product->order_items_count }}</span>
                            </li>
                        @empty
                            <li class="list-group-item text-center text-muted">Không có dữ liệu</li>
                        @endforelse
                    </ul>
                    <a href="{{ route('admin.reports.products') }}" class="btn btn-primary mt-2">Xem chi tiết</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Top 10 Khách Hàng</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        @forelse($topCustomers ?? [] as $customer)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $customer->name }} ({{ $customer->email }})
                                <span class="badge bg-success rounded-pill">₫{{ number_format($customer->total_spent, 0, ',', '.') }}</span>
                            </li>
                        @empty
                            <li class="list-group-item text-center text-muted">Không có dữ liệu</li>
                        @endforelse
                    </ul>
                    <a href="{{ route('admin.reports.customers') }}" class="btn btn-primary mt-2">Xem chi tiết</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
