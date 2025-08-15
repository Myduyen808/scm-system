@extends('layouts.app')

@section('title', 'Quản Lý Kho')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4">
                <i class="fas fa-warehouse"></i> Quản Lý Kho
            </h1>
            <a href="{{ route('admin.inventory.create') }}" class="btn btn-primary mb-3">Thêm Sản Phẩm</a>
        </div>
    </div>

    <div class="row">
        @forelse($products as $product)
            <div class="col-md-4 mb-3">
                <div class="card">
                    <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/250x200' }}" class="card-img-top" alt="{{ $product->name }}">
                    <div class="card-body">
                        <h5>{{ $product->name }}</h5>
                        <p>Giá: ₫{{ number_format($product->regular_price) }}</p>
                        <p>Tồn: {{ $product->stock_quantity }}</p>
                        <p>Nhà cung cấp: {{ $product->supplier ? $product->supplier->name : 'Chưa có' }}</p>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-md-12">
                <p class="text-center">Chưa có sản phẩm nào trong kho.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
