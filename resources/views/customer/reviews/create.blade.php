@extends('layouts.app')

@section('title', 'Đánh Giá Sản Phẩm - Khách Hàng')

@section('content')
<div class="container">
    <h1 class="mb-4"><i class="fas fa-star"></i> Đánh Giá Sản Phẩm</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('customer.reviews.store', $product->id) }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="rating" class="form-label">Đánh giá (1-5 sao)</label>
            <select name="rating" id="rating" class="form-control" required>
                <option value="">Chọn số sao</option>
                @for($i = 1; $i <= 5; $i++)
                    <option value="{{ $i }}">{{ $i }} sao</option>
                @endfor
            </select>
        </div>
        <div class="mb-3">
            <label for="comment" class="form-label">Nhận xét</label>
            <textarea name="comment" id="comment" class="form-control" rows="4" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Gửi đánh giá</button>
        <a href="{{ route('customer.products.for-review') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
@endsection
