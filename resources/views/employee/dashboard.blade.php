@extends('layouts.app')

@section('title', 'Employee Dashboard')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4"><i class="fas fa-user-tie"></i> Employee Dashboard</h1>
            <p class="text-muted">Quản lý các công việc hàng ngày của bạn:</p>
        </div>
    </div>

    <div class="row">
        @can('manage inventory')
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-primary">
                <div class="card-body text-center">
                    <i class="fas fa-warehouse fa-4x text-primary mb-3"></i>
                    <h5 class="card-title">Quản lý kho</h5>
                    <p class="card-text">Cập nhật tồn kho và kiểm tra sản phẩm</p>
                    <a href="{{ route('employee.inventory') }}" class="btn btn-primary btn-block">
                        <i class="fas fa-arrow-right"></i> Vào quản lý
                    </a>
                </div>
            </div>
        </div>
        @endcan

        @can('manage orders')
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-success">
                <div class="card-body text-center">
                    <i class="fas fa-tasks fa-4x text-success mb-3"></i>
                    <h5 class="card-title">Xử lý đơn hàng</h5>
                    <p class="card-text">Duyệt và cập nhật trạng thái đơn hàng</p>
                    <a href="{{ route('employee.orders') }}" class="btn btn-success btn-block">
                        <i class="fas fa-arrow-right"></i> Vào quản lý
                    </a>
                </div>
            </div>
        </div>
        @endcan

        @can('support customer')
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-info">
                <div class="card-body text-center">
                    <i class="fas fa-headset fa-4x text-info mb-3"></i>
                    <h5 class="card-title">Hỗ trợ khách hàng</h5>
                    <p class="card-text">Trả lời yêu cầu và ticket</p>
                    <a href="{{ route('employee.employeeSupport') }}" class="btn btn-info btn-block">
                        <i class="fas fa-arrow-right"></i> Vào quản lý
                    </a>
                </div>
            </div>
        </div>
        @endcan

        @can('manage reviews')
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

        @can('manage inventory')
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-secondary">
                <div class="card-body text-center">
                    <i class="fas fa-truck-loading fa-4x text-secondary mb-3"></i>
                    <h5 class="card-title">Yêu cầu nhập hàng</h5>
                    <p class="card-text">Gửi yêu cầu nhập hàng cho nhà cung cấp</p>
                    <a href="{{ route('employee.requests') }}" class="btn btn-secondary btn-block">
                        <i class="fas fa-arrow-right"></i> Gửi yêu cầu
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
                        <div><h4>{{ $totalProducts }}</h4><p class="mb-0">Tổng sản phẩm</p></div>
                        <i class="fas fa-box fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div><h4>{{ $pendingOrders }}</h4><p class="mb-0">Đơn hàng chờ</p></div>
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div><h4>{{ $openTickets }}</h4><p class="mb-0">Ticket mở</p></div>
                        <i class="fas fa-exclamation-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div><h4>{{ $activePromotions }}</h4><p class="mb-0">Khuyến mãi hoạt động</p></div>
                        <i class="fas fa-tags fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5>Thống kê trạng thái đơn hàng</h5>
                    <div style="height: 250px;"><canvas id="orderStatsChart"></canvas></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5>Doanh thu theo tháng</h5>
                    <div style="height: 250px;"><canvas id="revenueChart"></canvas></div>
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
            labels: ['Chờ xử lý', 'Đang xử lý', 'Hoàn thành'],
            datasets: [{
                label: 'Số lượng đơn hàng',
                data: [{{ $orderStats['pending'] }}, {{ $orderStats['processing'] }}, {{ $orderStats['delivered'] }}],
                backgroundColor: ['#007bff', '#ffc107', '#28a745'],
                borderColor: ['#0056b3', '#ffca28', '#218838'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: true, position: 'top' } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
        }
    });

    const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
    if (ctxRevenue) {
        new Chart(ctxRevenue, {
            type: 'line',
            data: {
                labels: @json($labels),
                datasets: [{
                    label: 'Doanh Thu (VNĐ)',
                    data: @json($revenueData),
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
                    y: { beginAtZero: true, title: { display: true, text: 'Doanh Thu (VNĐ)' }, ticks: { callback: value => value.toLocaleString('vi-VN') + ' VNĐ' } },
                    x: { title: { display: true, text: 'Tháng' } }
                }
            }
        });
    }
</script>
@endsection
