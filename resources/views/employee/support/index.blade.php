@extends('layouts.app')

@section('title', 'Hỗ Trợ Khách Hàng - Nhân Viên')

@section('content')
<div class="container">
    <h1><i class="fas fa-headset"></i> Hỗ Trợ Khách Hàng</h1>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Chủ đề</th>
                <th>Trạng thái</th>
                <th>Phản hồi gần nhất</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tickets as $ticket)
            <tr>
                <td>{{ $ticket->subject }}</td>
                <td>{{ $ticket->status }}</td>
                <td>
                    @if($ticket->replies->count() > 0)
                        {{ $ticket->replies->last()->message }}<br>
                        <small>{{ $ticket->replies->last()->created_at->format('d/m/Y H:i') }}</small>
                        ({{ $ticket->replies->last()->user_id === Auth::id() ? 'Bạn' : 'Khách hàng' }})
                    @else
                        Chưa có phản hồi
                    @endif
                </td>
                <td>
                    @if(in_array($ticket->status, ['assigned', 'replied', 'customer_replied']) && $ticket->assigned_to === Auth::id())
                        <a href="{{ route('employee.tickets.show', $ticket->id) }}" class="btn btn-info btn-sm">Xem</a>
                    @else
                        <span class="text-muted">Không thể thao tác</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center">Không có ticket</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    {{ $tickets->links() }}
</div>
@endsection
