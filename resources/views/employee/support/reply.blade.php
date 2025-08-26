@extends('layouts.app')

@section('title', 'Trả Lời Ticket - Nhân Viên')

@section('content')
<div class="container">
    <h1><i class="fas fa-reply"></i> Trả Lời Ticket: {{ $ticket->subject }}</h1>
    @if($ticket->assigned_to !== Auth::id())
        <div class="alert alert-danger">
            Bạn không được phép trả lời ticket này vì nó chưa được phân công cho bạn!
        </div>
        <a href="{{ route('employee.employeeSupport') }}" class="btn btn-secondary">Quay lại</a>
    @else
        <div class="card mb-3">
            <div class="card-body">
                <p><strong>Mô tả:</strong> {{ $ticket->description }}</p>
                <p><strong>Trạng thái:</strong> {{ $ticket->status }}</p>
            </div>
        </div>
        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('employee.replyTicket', $ticket->id) }}" method="POST">
            @csrf
            @method('PATCH')
            <div class="mb-3">
                <label for="reply" class="form-label">Phản hồi</label>
                <textarea name="response" class="form-control" id="reply" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Gửi phản hồi</button>
            <a href="{{ route('employee.employeeSupport') }}" class="btn btn-secondary">Quay lại</a>
        </form>
    @endif
</div>
@endsection
