@extends('layouts.app')

@section('title', 'Hỗ Trợ Khách Hàng - Nhân Viên')

@section('content')
<div class="container">
    <h1><i class="fas fa-headset"></i> Hỗ Trợ Khách Hàng</h1>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Chủ đề</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tickets as $ticket)
            <tr>
                <td>{{ $ticket->subject }}</td>
                <td>{{ $ticket->status }}</td>
                <td>
                <form action="{{ route('employee.replyTicket', $ticket->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-info btn-sm">Trả lời</button>
                </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="text-center">Không có ticket</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    {{ $tickets->links() }} <!-- Đảm bảo dòng này tồn tại và không bị lỗi cú pháp -->
</div>
@endsection
