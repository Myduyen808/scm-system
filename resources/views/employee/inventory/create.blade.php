@extends('layouts.app')

@section('title', 'Thêm Sản Phẩm - Nhân Viên')

@section('content')
<div class="container">
    <h1><i class="fas fa-plus"></i> Thêm Sản Phẩm</h1>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('employee.inventory.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Tên sản phẩm</label>
            <input type="text" name="name" class="form-control" id="name" required>
        </div>
        <div class="mb-3">
            <label for="regular_price" class="form-label">Giá gốc (VNĐ)</label>
            <input type="number" step="0.01" name="regular_price" class="form-control" id="regular_price" required>
        </div>
        <div class="mb-3">
            <label for="stock_quantity" class="form-label">Số lượng tồn</label>
            <input type="number" name="stock_quantity" class="form-control" id="stock_quantity" required>
        </div>
        <div class="mb-3">
            <label for="sku" class="form-label">SKU</label>
            <input type="text" name="sku" class="form-control" id="sku" required>
        </div>
        <div class="mb-3">
            <label for="image" class="form-label">Hình ảnh sản phẩm</label>
            <input type="file" name="image" class="form-control" id="image" accept="image/*">
        </div>
        <button type="submit" class="btn btn-primary">Lưu sản phẩm</button>
        <a href="{{ route('employee.inventory') }}" class="btn btn-secondary">Hủy</a>
    </form>
</div>
@endsection
