@extends('layouts.app')

@section('title', 'Quản Lý Khuyến Mãi - Nhân Viên')

@section('content')
<div class="container">
    <h1><i class="fas fa-tags"></i> Quản Lý Khuyến Mãi</h1>
    <a href="{{ route('employee.promotions.create') }}" class="btn btn-primary mb-3">Thêm khuyến mãi</a>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Tên khuyến mãi</th>
                <th>Giảm giá (%)</th>
                <th>Ngày bắt đầu</th>
                <th>Ngày kết thúc</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @forelse($promotions as $promotion)
            <tr>
                <td>{{ $promotion->name }}</td>
                <td>{{ $promotion->discount }}</td>
                <td>{{ $promotion->start_date }}</td>
                <td>{{ $promotion->end_date }}</td>
                <td>
                    <a href="{{ route('employee.promotions.edit', $promotion) }}" class="btn btn-warning btn-sm">Sửa</a>
                    <form action="{{ route('employee.promotions.destroy', $promotion) }}" method="POST" onsubmit="return confirm('Bạn có chắc?');" class="d-inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">Không có khuyến mãi</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    {{ $promotions->links() }}
</div>
@endsection
