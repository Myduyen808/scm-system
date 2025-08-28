@extends('layouts.app')

@section('title', 'Phản Hồi Ticket - Nhân Viên')

@section('content')
    <div class="container">
        <h1><i class="fas fa-reply"></i> Phản Hồi Ticket: {{ $ticket->subject }}</h1>
        <div class="card">
            <div class="card-body">
                <p><strong>Mô tả:</strong> {{ $ticket->description }}</p>
                <p><strong>Trạng thái:</strong> {{ $ticket->status }}</p>
                <form action="{{ route('employee.tickets.reply', $ticket->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="mb-3">
                        <label for="message" class="form-label">Phản hồi</label>
                        <textarea name="message" class="form-control" rows="4" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Gửi phản hồi</button>
                    <a href="{{ route('employee.employeeSupport') }}" class="btn btn-secondary">Quay lại</a>
                </form>
            </div>
        </div>
    </div>
@endsection
