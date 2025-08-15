@extends('layouts.app')

@section('title', 'Phản Hồi Yêu Cầu - Nhân Viên')

@section('content')
<div class="container">
    <h1><i class="fas fa-inbox"></i> Phản Hồi Yêu Cầu</h1>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Mã yêu cầu</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requests as $request)
            <tr>
                <td>{{ $request->id }}</td>
                <td>{{ $request->status ?? 'Chờ xử lý' }}</td>
                <td>
                    <form action="{{ route('employee.requests.process', $request) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm">Xử lý</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="text-center">Không có yêu cầu</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
