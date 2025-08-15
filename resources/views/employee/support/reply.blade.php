@extends('layouts.app')

@section('title', 'Trả Lời Ticket - Nhân Viên')

@section('content')
<div class="container">
    <h1><i class="fas fa-reply"></i> Trả Lời Ticket: {{ $ticket->subject }}</h1>
    <div class="card mb-3">
        <div class="card-body">
            <p><strong>Mô tả:</strong> {{ $ticket->description }}</p>
            <p><strong>Trạng thái:</strong> {{ $ticket->status }}</p>
        </div>
    </div>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('employee.support.store-reply', $ticket) }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="reply" class="form-label">Phản hồi</label>
            <textarea name="reply" class="form-control" id="reply" rows="5" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Gửi phản hồi</button>
        <a href="{{ route('employee.support') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
@endsection
