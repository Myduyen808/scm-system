@extends('layouts.app')

@section('title', 'Báo Cáo Doanh Thu - Nhân Viên')

@section('content')
<div class="container">
    <h1><i class="fas fa-chart-bar"></i> Báo Cáo Doanh Thu</h1>
    <form method="GET" class="mb-3">
        <div class="row">
            <div class="col-md-4">
                <select name="month" class="form-control" onchange="this.form.submit()">
                    @for ($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-4">
                <select name="year" class="form-control" onchange="this.form.submit()">
                    @for ($i = 2023; $i <= date('Y'); $i++)
                        <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
        </div>
    </form>
    <div class="card">
        <div class="card-body">
            <h5>Doanh thu: ₫{{ number_format($revenue, 0, ',', '.') }}</h5>
            <h5>Sản phẩm bán chạy:</h5>
            <ul class="list-group">
                @forelse($topProducts as $product)
                <li class="list-group-item">{{ $product->name }} ({{ $product->order_items_count }} đơn)</li>
                @empty
                <li class="list-group-item">Không có dữ liệu</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
