@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">Giám sát hoạt động</h1>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-striped table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Hành động</th>
                        <th>Mô hình</th>
                        <th>Người dùng</th>
                        <th>Thay đổi</th>
                        <th>Thời gian</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                        <tr>
                            <td>{{ $log->description ?? 'Không có mô tả' }}</td>
                            <td>{{ $log->log_name ?? 'Không xác định' }}</td>
                            <td>{{ $log->causer->name ?? 'Hệ thống' }}</td>
                            <td>
                                @if($log->properties)
                                    <ul class="list-unstyled small text-muted">
                                        @foreach($log->properties->getAttributes() as $key => $value)
                                            <li><strong>{{ $key }}:</strong> {{ $value }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    Không có thay đổi
                                @endif
                            </td>
                            <td>{{ $log->created_at->format('H:i d/m/Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $logs->links() }}
    </div>
</div>
@endsection
