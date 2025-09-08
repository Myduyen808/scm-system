@extends('layouts.app')

@section('title', 'Hỗ Trợ Khách Hàng - Khách Hàng')

@section('content')
<div class="container">
    <h1 class="mb-4"><i class="fas fa-headset"></i> Hỗ Trợ Khách Hàng</h1>
    <div class="card fade-in">
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @foreach($tickets as $ticket)
                @if(session("ticket_response_{$ticket->id}"))
                    <div class="alert alert-success">{{ session("ticket_response_{$ticket->id}") }}</div>
                @endif
            @endforeach
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <!-- Thêm trước bảng danh sách ticket -->
            @forelse ($notifications as $notification)
                <div class="alert alert-info mb-2">
                    {{ $notification->message }}
                    <a href="{{ route('customer.showSupport', $notification->related_id) }}" class="btn btn-sm btn-primary">Xem ngay</a>
                    <form action="{{ route('notifications.mark-as-read', $notification->id) }}" method="POST" style="display:inline;" class="d-none">
                        @csrf
                        @method('PATCH')
                    </form>
                </div>
            @empty
                @if (!$tickets->isEmpty())
                    <p class="text-muted mb-3">Không có thông báo mới.</p>
                @endif
            @endforelse

            <a href="{{ route('customer.createSupport') }}" class="btn btn-primary mb-3"><i class="fas fa-plus"></i> Tạo yêu cầu mới</a>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Mã yêu cầu</th>
                        <th>Tiêu đề</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                    <tr>
                        <td>{{ $ticket->id }}</td>
                        <td>
                            {{ $ticket->subject }}

                            {{-- Hiển thị tất cả phản hồi của ticket --}}
                            @if($ticket->replies->count() > 0)
                                <ul class="mt-1 mb-0">
                                    @foreach($ticket->replies as $reply)
                                        <li class="small text-{{ $reply->user_id === Auth::id() ? 'primary' : 'success' }}">
                                            {{ $reply->user_id === Auth::id() ? 'Bạn:' : 'Nhân viên:' }} {{ $reply->message }}
                                            <br>
                                            <small>{{ $reply->created_at->format('d/m/Y H:i') }}</small>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </td>
                        <td>{{ $ticket->status }}</td>
                        <td>{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <a href="{{ route('customer.showSupport', $ticket->id) }}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center">Chưa có yêu cầu nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $tickets->links() }}
        </div>
    </div>
</div>
@endsection
