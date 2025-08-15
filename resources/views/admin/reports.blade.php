@extends('layouts.app')

@section('title', 'Báo Cáo Doanh Thu')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4">
                <i class="fas fa-chart-bar"></i> Báo Cáo Doanh Thu
            </h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5>Doanh Thu Theo Tháng</h5>
                    <canvas id="revenueChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5>Tổng Doanh Thu</h5>
                    <p class="text-success">₫{{ number_format($revenue, 0, ',', '.') }}</p>
                    <h5>Sản Phẩm Bán Chạy</h5>
                    <ul>
                        @foreach($topProducts as $product)
                            <li>{{ $product->name }} ({{ $product->order_items_count }} đơn)</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($months), // ['Jan', 'Feb', ...] từ Controller
            datasets: [{
                label: 'Doanh thu (VND)',
                data: @json($revenues), // [1000000, 2000000, ...] từ Controller
                backgroundColor: 'rgba(245, 158, 11, 0.6)', // Màu cam nhạt
                borderColor: 'rgba(245, 158, 11, 1)', // Màu cam đậm
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₫' + value.toLocaleString('vi-VN');
                        }
                    }
                }
            },
            plugins: {
                legend: { display: true },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return '₫' + context.parsed.y.toLocaleString('vi-VN');
                        }
                    }
                }
            }
        }
    });
</script>
@endsection
