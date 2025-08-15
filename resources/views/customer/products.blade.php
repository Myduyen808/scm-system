@extends('layouts.app')
@section('content')
<div class="container">
    <h1>Danh sách sản phẩm</h1>
    <div class="row">
        @foreach($products as $product)
        <div class="col-md-4">
            <div class="card">
                <img src="{{ $product->image ?? 'placeholder.jpg' }}" class="card-img-top">
                <div class="card-body">
                    <h5>{{ $product->name }}</h5>
                    <p>Giá: ₫{{ number_format($product->current_price) }}</p>
                    <a href="{{ route('customer.product.detail', $product->id) }}" class="btn btn-info">Chi tiết</a>
                    <button class="btn btn-primary add-to-cart" data-id="{{ $product->id }}">Thêm giỏ</button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
