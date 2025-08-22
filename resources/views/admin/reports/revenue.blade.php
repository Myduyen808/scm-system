@extends('layouts.app')

@section('title', 'Báo Cáo Doanh Thu')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="mb-0">
                    <i class="fas fa-chart-line text-primary"></i> Báo Cáo Doanh Thu
                </h1>
                <div>
                    <a href="{{ route('admin.reports.export.revenue') }}" class="btn btn-success me-2">
                        <i class="fas fa-download"></i> Xuất CSV
                    </a>
                    <a href="{{ route('admin.reports') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <canvas id="revenueChart" height="300"></canvas> <!-- Thêm chiều cao cố định -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'],
            datasets: [{
                label: 'Doanh Thu (triệu ₫)',
                data: [
                    {{ isset($revenueByMonth[1]) ? round($revenueByMonth[1] / 1000000, 2) : 0 }},
                    {{ isset($revenueByMonth[2]) ? round($revenueByMonth[2] / 1000000, 2) : 0 }},
                    {{ isset($revenueByMonth[3]) ? round($revenueByMonth[3] / 1000000, 2) : 0 }},
                    {{ isset($revenueByMonth[4]) ? round($revenueByMonth[4] / 1000000, 2) : 0 }},
                    {{ isset($revenueByMonth[5]) ? round($revenueByMonth[5] / 1000000, 2) : 0 }},
                    {{ isset($revenueByMonth[6]) ? round($revenueByMonth[6] / 1000000, 2) : 0 }},
                    {{ isset($revenueByMonth[7]) ? round($revenueByMonth[7] / 1000000, 2) : 0 }},
                    {{ isset($revenueByMonth[8]) ? round($revenueByMonth[8] / 1000000, 2) : 0 }},
                    {{ isset($revenueByMonth[9]) ? round($revenueByMonth[9] / 1000000, 2) : 0 }},
                    {{ isset($revenueByMonth[10]) ? round($revenueByMonth[10] / 1000000, 2) : 0 }},
                    {{ isset($revenueByMonth[11]) ? round($revenueByMonth[11] / 1000000, 2) : 0 }},
                    {{ isset($revenueByMonth[12]) ? round($revenueByMonth[12] / 1000000, 2) : 0 }}
                ],
                borderColor: '#4CAF50',
                backgroundColor: 'rgba(76, 175, 80, 0.2)',
                borderWidth: 2,
                fill: true
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Doanh Thu (triệu ₫)'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Tháng'
                    }
                }
            },
            responsive: true,
            maintainAspectRatio: false
        }
    });
});
</script>
@endsection
