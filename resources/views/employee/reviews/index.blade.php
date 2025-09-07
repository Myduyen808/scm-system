@extends('layouts.app')

@section('title', 'Quản lý đánh giá')

@section('content')
<div class="container">
    <h1 class="mb-4"><i class="fas fa-star"></i> Quản lý đánh giá</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @forelse ($notifications as $notification)
        <div class="alert alert-info">
            {{ $notification->message }}
            <a href="{{ route('employee.reviews.show', $notification->related_id) }}" class="btn btn-sm btn-primary">Xem ngay</a>
            <form action="{{ route('notifications.mark-as-read', $notification->id) }}" method="POST" style="display:inline;" class="d-none">
                @csrf
                @method('PATCH')
            </form>
        </div>
    @empty
        @if (session('new_review_notification'))
            <div class="alert alert-info">
                {{ session('new_review_notification') }}
                <a href="{{ route('employee.reviews') }}" class="btn btn-sm btn-primary ml-2">Xem ngay</a>
            </div>
        @endif
    @endforelse

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Hình ảnh</th>
                        <th>ID</th>
                        <th>Sản phẩm</th>
                        <th>Khách hàng</th>
                        <th>Điểm</th>
                        <th>Nội dung</th>
                        <th>Ngày tạo</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reviews as $review)
                        <tr>
                            <td>
                                @if($review->product && $review->product->image)
                                    <img src="{{ Storage::url($review->product->image) }}" alt="{{ $review->product->name ?? 'N/A' }}" class="img-thumbnail" style="max-width: 50px; max-height: 50px;">
                                @else
                                    <img src="{{ asset('images/placeholder.jpg') }}" alt="No image" class="img-thumbnail" style="max-width: 50px; max-height: 50px;">
                                @endif
                            </td>
                            <td>{{ $review->id }}</td>
                            <td>{{ $review->product->name ?? 'N/A' }}</td>
                            <td>{{ $review->user->name ?? 'N/A' }}</td>
                            <td>{{ $review->rating }}</td>
                            <td>{{ Str::limit($review->comment, 50) }}</td>
                            <td>{{ $review->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('employee.reviews.show', $review) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> Xem
                                </a>
                                <form action="{{ route('employee.reviews.delete', $review) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc muốn xóa đánh giá này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i> Xóa
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $reviews->links() }}
        </div>
    </div>
</div>
@endsection
