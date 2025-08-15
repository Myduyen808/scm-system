@extends('layouts.app')

@section('title', 'Thêm Khuyến Mãi - Nhân Viên')

@section('content')
<div class="container">
    <h1><i class="fas fa-plus"></i> Thêm Khuyến Mãi</h1>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('employee.promotions.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Tên khuyến mãi</label>
            <input type="text" name="name" class="form-control" id="name" required>
        </div>
        <div class="mb-3">
            <label for="discount" class="form-label">Giảm giá (%)</label>
            <input type="number" step="0.01" name="discount" class="form-control" id="discount" required>
        </div>
        <div class="mb-3">
            <label for="start_date" class="form-label">Ngày bắt đầu</label>
            <input type="date" name="start_date" class="form-control" id="start_date" required>
        </div>
        <div class="mb-3">
            <label for="end_date" class="form-label">Ngày kết thúc</label>
            <input type="date" name="end_date" class="form-control" id="end_date" required>
        </div>
        <button type="submit" class="btn btn-primary">Lưu khuyến mãi</button>
        <a href="{{ route('employee.promotions') }}" class="btn btn-secondary">Hủy</a>
    </form>
</div>
@endsection
