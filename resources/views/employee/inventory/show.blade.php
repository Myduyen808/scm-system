@extends('layouts.app')

@section('title', 'Chi Tiết Sản Phẩm - Nhân Viên')

@section('content')
<div class="container">
    <h1><i class="fas fa-info-circle"></i> Chi Tiết Sản Phẩm: {{ $product->name }}</h1>
    <div class="card">
        <div class="card-body">
            <p><strong>SKU:</strong> {{ $product->sku }}</p>
            <p><strong>Giá gốc:</strong> ₫{{ number_format($product->regular_price, 0, ',', '.') }}</p>
            <p><strong>Số lượng tồn:</strong> {{ $product->stock_quantity }}</p>
            <a href="{{ route('employee.inventory') }}" class="btn btn-secondary">Quay lại</a>
        </div>
    </div>
</div>
@endsection
