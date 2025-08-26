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
            @if ($ticket->status === 'replied' && isset($reply))
                <div class="alert alert-success mt-3">
                    <strong>Phản hồi từ nhân viên:</strong> {{ $reply->message }}<br>
                    <small>Thời gian: {{ $reply->created_at->format('d/m/Y H:i') }}</small>
                </div>
            @endif
        </div>
    </div>
    <a href="{{ route('customer.viewTickets') }}" class="btn btn-secondary">Quay lại</a>
    @if ($ticket->status !== 'replied')
        <form action="{{ route('customer.replySupport', $ticket->id) }}" method="POST" class="d-inline">
            @csrf
            <div class="form-group mt-2">
                <textarea name="message" class="form-control" rows="3" placeholder="Nhập phản hồi..." required></textarea>
            </div>
            <button type="submit" class="btn btn-primary mt-2">Gửi phản hồi</button>
        </form>
    @endif
</div>
@endsection
