@extends('layouts.app')

@section('title', 'Hỗ Trợ Khách Hàng - Khách Hàng')

@section('content')
<div class="container">
    <h1 class="mb-4"><i class="fas fa-headset"></i> Hỗ Trợ Khách Hàng</h1>
    <div class="card fade-in">
        <div class="card-body">
            <a href="{{ route('customer.support.create') }}" class="btn btn-primary mb-3"><i class="fas fa-plus"></i> Tạo yêu cầu mới</a>
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
                        <td>{{ $ticket->ticket_number }}</td>
                        <td>{{ $ticket->title }}</td>
                        <td>{{ $ticket->status }}</td>
                        <td>{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <a href="{{ route('customer.support.show', $ticket->id) }}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
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
