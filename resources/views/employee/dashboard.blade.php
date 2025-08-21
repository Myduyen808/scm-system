@extends('layouts.app')

@section('title', 'Employee Dashboard')

@section('content')
@php
use App\Models\User;
@endphp

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4">
                <i class="fas fa-user-tie"></i> Employee Dashboard
            </h1>
            <p class="text-muted">Quản lý các quyền của bạn:</p>
        </div>
    </div>

    <div class="row">
        @can('manage inventory')
        <!-- Quản lý kho -->
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-primary">
                <div class="card-body text-center">
                    <i class="fas fa-warehouse fa-4x text-primary mb-3"></i>
                    <h5 class="card-title">Quản lý kho</h5>
                    <p class="card-text">Thêm, sửa, xóa sản phẩm; cập nhật tồn kho</p>
                    <a href="{{ route('employee.inventory') }}" class="btn btn-primary btn-block">
                        <i class="fas fa-arrow-right"></i> Vào quản lý
                    </a>
                </div>
            </div>
        </div>
        @endcan

        @can('manage orders')
        <!-- Xử lý đơn hàng -->
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-success">
                <div class="card-body text-center">
                    <i class="fas fa-tasks fa-4x text-success mb-3"></i>
                    <h5 class="card-title">Xử lý đơn hàng</h5>
                    <p class="card-text">Duyệt đơn hàng, cập nhật trạng thái, hủy</p>
                    <a href="{{ route('employee.orders') }}" class="btn btn-success btn-block">
                        <i class="fas fa-arrow-right"></i> Vào quản lý
                    </a>
                </div>
            </div>
        </div>
        @endcan

        @can('support customer')
        <!-- Hỗ trợ khách hàng -->
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-info">
                <div class="card-body text-center">
                    <i class="fas fa-headset fa-4x text-info mb-3"></i>
                    <h5 class="card-title">Hỗ trợ khách hàng</h5>
                    <p class="card-text">Trả lời câu hỏi và xử lý khiếu nại</p>
                    <a href="{{ route('employee.support') }}" class="btn btn-info btn-block">
                        <i class="fas fa-arrow-right"></i> Vào quản lý
                    </a>
                </div>
            </div>
        </div>
        @endcan

        @can('manage reviews')
        <!-- Quản lý đánh giá -->
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-warning">
                <div class="card-body text-center">
                    <i class="fas fa-star fa-4x text-warning mb-3"></i>
                    <h5 class="card-title">Quản lý đánh giá</h5>
                    <p class="card-text">Xem và xóa đánh giá sản phẩm</p>
                    <a href="{{ route('employee.reviews') }}" class="btn btn-warning btn-block">
                        <i class="fas fa-arrow-right"></i> Vào quản lý
                    </a>
                </div>
            </div>
        </div>
        @endcan
    </div>

    <!-- Danh sách đánh giá mới nhất -->
    @can('manage reviews')
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5>Danh sách đánh giá mới nhất</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Sản phẩm</th>
                                <th>Khách hàng</th>
                                <th>Điểm</th>
                                <th>Nội dung</th>
                                <th>Ngày tạo</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reviews as $review)
                                <tr>
                                    <td>{{ $review->id }}</td>
                                    <td>{{ $review->product->name ?? 'N/A' }}</td>
                                    <td>{{ $review->user->name ?? 'N/A' }}</td>
                                    <td>{{ $review->rating }}</td>
                                    <td>{{ Str::limit($review->comment, 50) }}</td>
                                    <td>{{ $review->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('employee.reviews.show', $review) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> Xem
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endcan

    <!-- Statistics Cards -->
    <div class="row mt-4">
        <div class="col-md-3 mb-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $totalProducts }}</h4>
                            <p class="mb-0">Tổng sản phẩm</p>
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
                            <h4>{{ $pendingOrders }}</h4>
                            <p class="mb-0">Đơn hàng chờ</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
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
                            <h4>{{ $openTickets }}</h4>
                            <p class="mb-0">Ticket mở</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-circle fa-2x"></i>
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
                            <h4>{{ $activePromotions }}</h4>
                            <p class="mb-0">Khuyến mãi đang hoạt động</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-tags fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Biểu đồ trạng thái đơn hàng -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5>Thống kê trạng thái đơn hàng</h5>
                    <canvas id="orderStatsChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('orderStatsChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Chờ xử lý', 'Đang xử lý', 'Hoàn thành'],
            datasets: [{
                label: 'Số lượng đơn hàng',
                data: [{{ $orderStats['pending'] }}, {{ $orderStats['processing'] }}, {{ $orderStats['completed'] }}],
                backgroundColor: ['#007bff', '#ffc107', '#28a745'],
                borderColor: ['#0056b3', '#ffca28', '#218838'],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endsection
