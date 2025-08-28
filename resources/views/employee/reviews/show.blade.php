@extends('layouts.app')

@section('title', 'Chi tiết đánh giá')

@section('content')
<div class="container">
    <h1 class="mb-4"><i class="fas fa-star"></i> Chi tiết đánh giá</h1>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Đánh giá #{{ $review->id }}</h5>
            <p><strong>Hình ảnh sản phẩm:</strong>
                @if($review->product && $review->product->image)
                    <img src="{{ Storage::url($review->product->image) }}" alt="{{ $review->product->name ?? 'N/A' }}" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                @else
                    <img src="{{ asset('images/placeholder.jpg') }}" alt="No image" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                @endif
            </p>
            <p><strong>Sản phẩm:</strong> {{ $review->product->name ?? 'N/A' }}</p>
            <p><strong>Khách hàng:</strong> {{ $review->user->name ?? 'N/A' }}</p>
            <p><strong>Điểm:</strong> {{ $review->rating }}</p>
            <p><strong>Nội dung:</strong> {{ $review->comment }}</p>
            <p><strong>Ngày tạo:</strong> {{ $review->created_at->format('d/m/Y H:i') }}</p>
            <a href="{{ route('employee.reviews') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>
</div>
@endsection
