@extends('layouts.app')

@section('title', 'Báo Cáo Sản Phẩm - Nhà Cung Cấp')

@section('content')
<div class="container">
    <h1 class="mb-4"><i class="fas fa-chart-line"></i> Báo Cáo Sản Phẩm</h1>
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
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Ảnh</th>
                <th>Tên sản phẩm</th>
                <th>Số lượng bán</th>
                <th>Doanh thu</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reports as $report)
            <tr>
                <td>
                    <img src="{{ $report['image'] ? Storage::url($report['image']) : 'https://via.placeholder.com/100x100?text=No+Image' }}" alt="{{ $report['name'] }}" style="max-width: 100px; max-height: 100px; object-fit: cover;" class="img-fluid">
                </td>
                <td>{{ $report['name'] }}</td>
                <td>{{ $report['sold_quantity'] ?? 0 }}</td>
                <td>₫{{ number_format($report['revenue'] ?? 0, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center">Không có dữ liệu</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
