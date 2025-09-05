@extends('layouts.app')

@section('title', 'Chi Tiết Yêu Cầu - Khách Hàng')

@section('content')
<div class="container">
    <h1><i class="fas fa-ticket"></i> Chi Tiết Yêu Cầu: {{ $ticket->subject }}</h1>

    <div class="card mb-3">
        <div class="card-body">
            <p><strong>Mô tả:</strong> {{ $ticket->description }}</p>
            <p><strong>Trạng thái:</strong> {{ $ticket->status }}</p>
            <p><strong>Ngày tạo:</strong> {{ $ticket->created_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <!-- Hiển thị tất cả phản hồi -->
    @if ($ticket->replies->count() > 0)
        @foreach($ticket->replies as $reply)
            <div class="alert alert-{{ $reply->user_id === Auth::id() ? 'primary' : 'success' }} mb-2">
                <strong>{{ $reply->user_id === Auth::id() ? 'Bạn:' : 'Nhân viên:' }}</strong> {{ $reply->message }}
                <br>
                <small>{{ $reply->created_at->format('d/m/Y H:i') }}</small>
            </div>
        @endforeach
    @endif

    <!-- Form gửi phản hồi của khách hàng (luôn hiển thị nếu ticket chưa đóng) -->
    @if (!in_array($ticket->status, ['closed']))
        <form action="{{ route('customer.replySupport', $ticket->id) }}" method="POST" class="mt-3">
            @csrf
            <div class="form-group">
                <textarea name="message" class="form-control" rows="3" placeholder="Nhập phản hồi..." required></textarea>
            </div>
            <button type="submit" class="btn btn-primary mt-2">Gửi phản hồi</button>
        </form>
    @endif

    <a href="{{ route('customer.viewTickets') }}" class="btn btn-secondary mt-3">Quay lại</a>
</div>
@endsection
