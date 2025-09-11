@extends('layouts.app')

@section('title', 'Dashboard của ' . ($supplier->name ?? 'Nhà cung cấp'))

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4">
                <i class="fas fa-truck"></i> Dashboard của {{ $supplier->name ?? 'Nhà cung cấp' }}
            </h1>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-4 mb-4 fade-in">
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

        <div class="col-md-4 mb-4 fade-in">
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

        <div class="col-md-4 mb-4 fade-in">
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

        <div class="col-md-4 mb-4 fade-in">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <i class="fas fa-chart-pie fa-3x text-warning mb-3"></i>
                    <h5 class="card-title">Báo cáo sản phẩm</h5>
                    <p class="card-text">Xem doanh thu và số lượng bán</p>
                    <a href="{{ route('supplier.products.report') }}" class="btn btn-warning">
                        <i class="fas fa-arrow-right"></i> Xem báo cáo
                    </a>
                </div>
            </div>
        </div>

        <!-- Thêm thẻ mới cho Yêu cầu nội bộ -->
        <div class="col-md-4 mb-4 fade-in">
            <div class="card border-secondary">
                <div class="card-body text-center">
                    <i class="fas fa-file-alt fa-3x text-secondary mb-3"></i>
                    <h5 class="card-title">Yêu cầu Nội Bộ</h5>
                    <p class="card-text">Tạo yêu cầu nội bộ cho nhân viên/sự kiện</p>
                    <a href="{{ route('supplier.internalRequestForm') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right"></i> Tạo yêu cầu
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4 fade-in">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $pendingApprovalCount ?? 0 }}</h4>
                            <p class="mb-0">Sản phẩm chờ duyệt</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-hourglass-half fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4 fade-in">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $totalProducts ?? 0 }}</h4>
                            <p class="mb-0">Sản phẩm cung cấp</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-box fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4 fade-in">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $pendingOrders ?? 0 }}</h4>
                            <p class="mb-0">Đơn hàng chờ xử lý</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4 fade-in">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>₫{{ number_format($monthlyRevenue ?? 0, 0, ',', '.') }}</h4>
                            <p class="mb-0">Doanh thu tháng</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-dollar-sign fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4 fade-in">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $pendingInternalRequests ?? 0 }}</h4>
                            <p class="mb-0">Yêu cầu nội bộ chờ duyệt</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-file-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart for Monthly Revenue -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Doanh thu theo tháng</h5>
                    <div style="height: 300px;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart for Season Distribution (only for supplier ID 4) -->
    @if(Auth::id() == 4)
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Phân phối sản phẩm theo mùa</h5>
                    <div style="height: 300px;">
                        <canvas id="seasonChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
        const labels = @json($labels ?? []);
        const data = @json($data ?? []);

        if (labels.length === 0 || data.length === 0) {
            labels.push('Không có dữ liệu');
            data.push(0);
        }

        new Chart(ctxRevenue, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Doanh thu (VND)',
                    data: data,
                    borderColor: '#36A2EB',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) label += ': ';
                                if (context.parsed.y !== null) label += '₫' + context.parsed.y.toLocaleString('vi-VN');
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Doanh thu (VND)' },
                        ticks: { callback: value => '₫' + value.toLocaleString('vi-VN') }
                    },
                    x: { title: { display: true, text: 'Tháng' } }
                }
            }
        });

        if (@json(Auth::id() == 4)) {
            const ctxSeason = document.getElementById('seasonChart').getContext('2d');
            const seasonLabels = @json($seasonLabels ?? []);
            const seasonData = @json($seasonData ?? []).map(value => Math.max(Math.ceil(value), 1));

            if (seasonLabels.length === 0 || seasonData.length === 0) {
                seasonLabels.push('Không có dữ liệu');
                seasonData.push(1);
            }

            new Chart(ctxSeason, {
                type: 'bar',
                data: {
                    labels: seasonLabels,
                    datasets: [{
                        label: 'Số lượng sản phẩm',
                        data: seasonData,
                        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0'],
                        borderColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'top' } },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: { display: true, text: 'Số lượng' },
                            ticks: { stepSize: 1, callback: value => Number.isInteger(value) ? value : null }
                        }
                    }
                }
            });
        }
    });
</script>
@endsection
