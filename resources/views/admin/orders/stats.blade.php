@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Thống kê doanh thu</h1>

    <p>Tổng doanh thu: {{ isset($totalRevenue) ? number_format($totalRevenue, 0, ',', '.') : '0' }} ₫</p>
    <p class="text-muted"><small>Tính từ các đơn hàng đã giao thành công - status 'delivered'</small></p>

    @if (!isset($monthlyRevenue) || $monthlyRevenue->isEmpty())
        <p class="text-muted">Không có dữ liệu doanh thu theo tháng.</p>
    @else

    <!-- Thêm thẻ canvas cho biểu đồ -->
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <canvas id="monthlyRevenueChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Tháng</th>
                    <th>Doanh thu</th>
                </tr>
            </thead>
            <tbody>
                @foreach($monthlyRevenue as $item)
                <tr>
                    <td>Tháng {{ $item->month }}</td>
                    <td>₫{{ number_format($item->revenue, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('monthlyRevenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'],
            datasets: [{
                label: 'Doanh Thu (triệu ₫)',
                data: [
                    {{ $monthlyRevenue->where('month', 1)->first() ? round($monthlyRevenue->where('month', 1)->first()->revenue / 1000000, 2) : 0 }},
                    {{ $monthlyRevenue->where('month', 2)->first() ? round($monthlyRevenue->where('month', 2)->first()->revenue / 1000000, 2) : 0 }},
                    {{ $monthlyRevenue->where('month', 3)->first() ? round($monthlyRevenue->where('month', 3)->first()->revenue / 1000000, 2) : 0 }},
                    {{ $monthlyRevenue->where('month', 4)->first() ? round($monthlyRevenue->where('month', 4)->first()->revenue / 1000000, 2) : 0 }},
                    {{ $monthlyRevenue->where('month', 5)->first() ? round($monthlyRevenue->where('month', 5)->first()->revenue / 1000000, 2) : 0 }},
                    {{ $monthlyRevenue->where('month', 6)->first() ? round($monthlyRevenue->where('month', 6)->first()->revenue / 1000000, 2) : 0 }},
                    {{ $monthlyRevenue->where('month', 7)->first() ? round($monthlyRevenue->where('month', 7)->first()->revenue / 1000000, 2) : 0 }},
                    {{ $monthlyRevenue->where('month', 8)->first() ? round($monthlyRevenue->where('month', 8)->first()->revenue / 1000000, 2) : 0 }},
                    {{ $monthlyRevenue->where('month', 9)->first() ? round($monthlyRevenue->where('month', 9)->first()->revenue / 1000000, 2) : 0 }},
                    {{ $monthlyRevenue->where('month', 10)->first() ? round($monthlyRevenue->where('month', 10)->first()->revenue / 1000000, 2) : 0 }},
                    {{ $monthlyRevenue->where('month', 11)->first() ? round($monthlyRevenue->where('month', 11)->first()->revenue / 1000000, 2) : 0 }},
                    {{ $monthlyRevenue->where('month', 12)->first() ? round($monthlyRevenue->where('month', 12)->first()->revenue / 1000000, 2) : 0 }}
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
