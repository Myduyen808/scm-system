@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4">
                <i class="fas fa-tachometer-alt"></i> Admin Dashboard
            </h1>
            <p class="text-muted">Quản lý toàn bộ hệ thống:</p>
        </div>
    </div>

    <div class="row">
        @can('manage inventory')
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-primary">
                <div class="card-body text-center">
                    <i class="fas fa-warehouse fa-3x text-primary mb-3"></i>
                    <h5 class="card-title">Quản lý kho</h5>
                    <p class="card-text">Thêm, sửa, xóa sản phẩm và cập nhật tồn kho</p>
                    <a href="{{ route('admin.inventory') }}" class="btn btn-primary btn-block">
                        <i class="fas fa-arrow-right"></i> Vào kho hàng
                    </a>
                </div>
            </div>
        </div>
        @endcan

        @can('manage orders')
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-success">
                <div class="card-body text-center">
                    <i class="fas fa-shopping-cart fa-3x text-success mb-3"></i>
                    <h5 class="card-title">Quản lý đơn hàng</h5>
                    <p class="card-text">Duyệt, cập nhật hoặc hủy đơn hàng</p>
                    <a href="{{ route('admin.orders') }}" class="btn btn-success btn-block">
                        <i class="fas fa-arrow-right"></i> Xem đơn hàng
                    </a>
                </div>
            </div>
        </div>
        @endcan

        @can('view reports')
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-info">
                <div class="card-body text-center">
                    <i class="fas fa-chart-bar fa-3x text-info mb-3"></i>
                    <h5 class="card-title">Báo cáo doanh thu</h5>
                    <p class="card-text">Xem thống kê sản phẩm bán chạy</p>
                    <a href="{{ route('admin.reports') }}" class="btn btn-info btn-block">
                        <i class="fas fa-arrow-right"></i> Xem báo cáo
                    </a>
                </div>
            </div>
        </div>
        @endcan

        @can('approve products')
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-warning">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle fa-3x text-warning mb-3"></i>
                    <h5 class="card-title">Phê duyệt sản phẩm</h5>
                    <p class="card-text">Duyệt sản phẩm từ nhà cung cấp</p>
                    <a href="{{ route('admin.pending.products') }}" class="btn btn-warning btn-block mt-2">Xem tất cả</a>
                </div>
            </div>
        </div>
        @endcan

        @can('manage inventory')
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-secondary">
                <div class="card-body text-center">
                    <i class="fas fa-plus fa-3x text-secondary mb-3"></i>
                    <h5 class="card-title">Thêm sản phẩm</h5>
                    <p class="card-text">Thêm sản phẩm mới vào kho</p>
                    <a href="{{ route('admin.inventory.create') }}" class="btn btn-secondary btn-block">
                        <i class="fas fa-arrow-right"></i> Thêm ngay
                    </a>
                </div>
            </div>
        </div>
        @endcan

        @can('manage users')
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-warning">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-3x text-warning mb-3"></i>
                    <h5 class="card-title">Quản lý người dùng</h5>
                    <p class="card-text">Thêm, sửa, xóa và quản lý vai trò người dùng</p>
                    <a href="{{ route('admin.users') }}" class="btn btn-warning btn-block">
                        <i class="fas fa-arrow-right"></i> Quản lý người dùng
                    </a>
                </div>
            </div>
        </div>
        @endcan

        @can('manage settings')
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-dark">
                <div class="card-body text-center">
                    <i class="fas fa-cog fa-3x text-dark mb-3"></i>
                    <h5 class="card-title">Cài đặt hệ thống</h5>
                    <p class="card-text">Cấu hình thông tin và cài đặt chung</p>
                    <a href="{{ route('admin.settings') }}" class="btn btn-dark btn-block">
                        <i class="fas fa-arrow-right"></i> Cài đặt
                    </a>
                </div>
            </div>
        </div>
        @endcan

        @can('manage promotions')
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-info">
                <div class="card-body text-center">
                    <i class="fas fa-tags fa-3x text-info mb-3"></i>
                    <h5 class="card-title">Quản lý khuyến mãi</h5>
                    <p class="card-text">Tạo, chỉnh sửa, kích hoạt khuyến mãi</p>
                    <a href="{{ route('admin.promotions') }}" class="btn btn-info btn-block">
                        <i class="fas fa-arrow-right"></i> Quản lý khuyến mãi
                    </a>
                </div>
            </div>
        </div>
        @endcan

    @can('manage tickets')
        @if($ticketToAssign)
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-danger">
                    <div class="card-body text-center">
                        <i class="fas fa-ticket-alt fa-3x text-danger mb-3"></i>
                        <h5 class="card-title">Quản lý ticket</h5>
                        <p class="card-text">Xem và phân công ticket cho nhân viên</p>
                        <a href="{{ route('admin.tickets') }}" class="btn btn-danger btn-block">
                            <i class="fas fa-arrow-right"></i> Quản lý ticket
                        </a>
                    </div>
                </div>
            </div>
        @else
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-secondary">
                    <div class="card-body text-center">
                        <i class="fas fa-ticket-alt fa-3x text-secondary mb-3"></i>
                        <h5 class="card-title">Quản lý ticket</h5>
                        <p class="card-text">Hiện không có ticket nào để phân công</p>
                        <button class="btn btn-secondary btn-block" disabled>Chưa có ticket</button>
                    </div>
                </div>
            </div>
        @endif
    @endcan

        @can('manage reviews')
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-primary">
                <div class="card-body text-center">
                    <i class="fas fa-star fa-3x text-primary mb-3"></i>
                    <h5 class="card-title">Quản lý đánh giá</h5>
                    <p class="card-text">Xem và quản lý đánh giá của khách hàng</p>
                    <a href="{{ route('admin.reviews') }}" class="btn btn-primary btn-block">
                        <i class="fas fa-arrow-right"></i> Xem đánh giá
                    </a>
                </div>
            </div>
        </div>
        @endcan

        @can('manage internal requests')
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-info">
                <div class="card-body text-center">
                    <i class="fas fa-file-alt fa-3x text-info mb-3"></i>
                    <h5 class="card-title">Quản lý yêu cầu nội bộ</h5>
                    <p class="card-text">Xem, duyệt và từ chối các yêu cầu của nhân viên và nhà cung cấp</p>
                    <a href="{{ route('admin.internalRequests') }}" class="btn btn-info btn-block">
                        <i class="fas fa-arrow-right"></i> Xem tất cả
                    </a>
                </div>
            </div>
        </div>
        @endcan
    </div>
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
                            <h4>{{ $lowStockProducts }}</h4>
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
                            <h4>{{ number_format($totalRevenue, 0, '.', ',') }} VNĐ</h4>
                            <p class="mb-0">Doanh thu tháng</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-dollar-sign fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card bg-dark text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $totalUsers }}</h4>
                            <p class="mb-0">Tổng người dùng</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card bg-secondary text-white">
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

        <div class="col-md-3 mb-4">
            <div class="card bg-danger text-white">
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
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $todayOrders }}</h4>
                            <p class="mb-0">Đơn hàng hôm nay</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-calendar-day fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top sản phẩm bán chạy -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5>Top 5 sản phẩm bán chạy</h5>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Tên sản phẩm</th>
                                <th>SKU</th>
                                <th>Số lượng bán</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($topProducts as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->sku }}</td>
                                <td>{{ $product->order_items_count }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Đơn hàng gần đây -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5>Đơn hàng gần đây</h5>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Mã đơn hàng</th>
                                <th>Khách hàng</th>
                                <th>Trạng thái</th>
                                <th>Ngày đặt</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentOrders as $order)
                            <tr>
                                <td>{{ $order->order_number }}</td>
                                <td>{{ $order->customer ? $order->customer->name : 'Khách vãng lai' }}</td>
                                <td>{{ $order->status }}</td>
                                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
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
                    <div style="height: 250px;">
                        <canvas id="orderStatsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Biểu đồ doanh thu theo tháng -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5>Doanh thu theo tháng</h5>
                    <div style="height: 250px;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctxOrderStats = document.getElementById('orderStatsChart').getContext('2d');
    new Chart(ctxOrderStats, {
        type: 'bar',
        data: {
            labels: {!! json_encode(['Chờ xử lý', 'Đang xử lý', 'Hoàn thành']) !!},
            datasets: [{
                label: 'Số lượng đơn hàng',
                data: {!! json_encode([$orderStats['pending'], $orderStats['processing'], $orderStats['delivered']]) !!},
                backgroundColor: ['#007bff', '#ffc107', '#28a745'],
                borderColor: ['#0056b3', '#ffca28', '#218838'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });

    const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
    if (ctxRevenue) {
        new Chart(ctxRevenue, {
            type: 'line',
            data: {
                labels: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'],
                datasets: [{
                    label: 'Doanh Thu (VNĐ)',
                    data: {!! json_encode($revenueData) !!},
                    borderColor: '#4CAF50',
                    backgroundColor: 'rgba(76, 175, 80, 0.2)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Doanh Thu (VNĐ)'
                        },
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('vi-VN') + ' VNĐ';
                            }
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Tháng'
                        }
                    }
                }
            }
        });
    }
</script>
@endsection
