@extends('layouts.app')

@section('title', 'Chi tiết Đánh giá')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4">
                <i class="fas fa-star"></i> Chi tiết Đánh giá
            </h1>
            <a href="{{ route('admin.reviews') }}" class="btn btn-secondary mb-3">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5>Thông tin đánh giá</h5>
                    <dl class="row">
                        <dt class="col-sm-3">Tên sản phẩm</dt>
                        <dd class="col-sm-9">{{ $review->product ? $review->product->name : 'Không xác định' }}</dd>

                        <dt class="col-sm-3">Khách hàng</dt>
                        <dd class="col-sm-9">{{ $review->user ? $review->user->name : 'Khách vãng lai' }}</dd>

                        <dt class="col-sm-3">Số sao</dt>
                        <dd class="col-sm-9">
                            {{ $review->rating }} <i class="fas fa-star text-warning"></i>
                        </dd>

                        <dt class="col-sm-3">Nội dung</dt>
                        <dd class="col-sm-9">{{ $review->comment }}</dd>

                        <dt class="col-sm-3">Ngày gửi</dt>
                        <dd class="col-sm-9">{{ $review->created_at->format('d/m/Y H:i') }}</dd>
                    </dl>

                    <form action="{{ route('admin.reviews.delete', $review->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Bạn có chắc muốn xóa đánh giá này?')">
                            <i class="fas fa-trash"></i> Xóa đánh giá
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
