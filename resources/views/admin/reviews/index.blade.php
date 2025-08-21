@extends('layouts.app')

@section('title', 'Quản lý Đánh giá')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4">
                <i class="fas fa-star"></i> Quản lý Đánh giá
            </h1>
            <p class="text-muted">Xem và quản lý đánh giá của khách hàng</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    @if($reviews->isEmpty())
                        <p class="text-center">Chưa có đánh giá nào.</p>
                    @else
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Tên sản phẩm</th>
                                    <th>Khách hàng</th>
                                    <th>Số sao</th>
                                    <th>Nội dung</th>
                                    <th>Ngày gửi</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($reviews as $review)
                                <tr>
                                    <td>{{ $review->product ? $review->product->name : 'Không xác định' }}</td>
                                    <td>{{ $review->user ? $review->user->name : 'Khách vãng lai' }}</td>
                                    <td>{{ $review->rating }} <i class="fas fa-star text-warning"></i></td>
                                    <td>{{ \Illuminate\Support\Str::limit($review->comment, 50, '...') }}</td>
                                    <td>{{ $review->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('admin.reviews.show', $review->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> Xem
                                        </a>
                                        <form action="{{ route('admin.reviews.delete', $review->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xóa đánh giá này?')">
                                                <i class="fas fa-trash"></i> Xóa
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $reviews->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
