@extends('layouts.app')

@section('title', 'Chi Tiết Ticket - Nhân Viên')

@section('content')
<div class="container">
    <h1><i class="fas fa-ticket"></i> Chi Tiết Ticket: {{ $ticket->subject }}</h1>

    <div class="card mb-3">
        <div class="card-body">
            <p><strong>Mô tả:</strong> {{ $ticket->description }}</p>
            <p><strong>Trạng thái:</strong> {{ $ticket->status }}</p>
            <p><strong>Khách hàng:</strong> {{ $ticket->user->name ?? 'Không xác định' }}</p>
            <p><strong>Ngày tạo:</strong> {{ $ticket->created_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <!-- Hiển thị lịch sử chat -->
    @if ($ticket->replies->count() > 0)
        @foreach($ticket->replies as $reply)
            <div class="alert alert-{{ $reply->user_id === Auth::id() ? 'primary' : 'success' }} mb-2">
                <strong>{{ $reply->user_id === Auth::id() ? 'Bạn:' : 'Khách hàng:' }}</strong> {{ $reply->message }}
                <br>
                <small>{{ $reply->created_at->format('d/m/Y H:i') }}</small>
            </div>
        @endforeach
    @else
        <div class="alert alert-info">Chưa có phản hồi nào.</div>
    @endif

    <!-- Form gửi phản hồi -->
    @if (!in_array($ticket->status, ['closed']))
        <form action="{{ route('employee.tickets.reply', $ticket->id) }}" method="POST" class="mt-3">
            @csrf
            @method('PATCH')
            <div class="mb-3">
                <label for="message" class="form-label">Phản hồi</label>
                <textarea name="message" class="form-control" rows="4" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Gửi phản hồi</button>
            <a href="{{ route('employee.employeeSupport') }}" class="btn btn-secondary">Quay lại</a>
        </form>
    @else
        <div class="alert alert-warning">Ticket đã đóng, không thể gửi phản hồi thêm.</div>
    @endif
</div>
@endsection
