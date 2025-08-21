@extends('layouts.app')

@section('title', 'Sản Phẩm Để Đánh Giá - Khách Hàng')

@section('content')
<div class="container">
    <h1 class="mb-4"><i class="fas fa-star"></i> Sản Phẩm Để Đánh Giá</h1>
    <p class="text-muted">Danh sách các sản phẩm đã giao mà bạn chưa đánh giá.</p>

    @if($productsForReview->isEmpty())
        <p class="text-center">Bạn không có sản phẩm nào để đánh giá.</p>
    @else
        <div class="row">
            @foreach($productsForReview as $product)
            <div class="col-md-4 mb-4">
                <div class="card">
                    <img src="{{ $product->image ? Storage::url($product->image) : 'https://via.placeholder.com/250x200' }}" class="card-img-top" alt="{{ $product->name }}">
                    <div class="card-body">
                        <h6 class="card-title">{{ $product->name }}</h6>
                        <p class="card-text">
                            @if($product->current_price && $product->current_price < ($product->sale_price ?? $product->regular_price))
                                <del class="text-muted">₫{{ number_format($product->regular_price, 0, ',', '.') }}</del>
                                <strong class="text-danger">₫{{ number_format($product->current_price, 0, ',', '.') }}</strong>
                            @elseif($product->sale_price && $product->sale_price < $product->regular_price)
                                <del class="text-muted">₫{{ number_format($product->regular_price, 0, ',', '.') }}</del>
                                <strong class="text-danger">₫{{ number_format($product->sale_price, 0, ',', '.') }}</strong>
                            @else
                                <strong>₫{{ number_format($product->regular_price, 0, ',', '.') }}</strong>
                            @endif
                        </p>
                        <a href="{{ route('customer.reviews.create', $product->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-star"></i> Đánh giá sản phẩm
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
