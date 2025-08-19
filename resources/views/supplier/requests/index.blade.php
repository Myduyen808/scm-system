@extends('layouts.app')

@section('title', 'Phản Hồi Yêu Cầu - Nhà Cung Cấp')

@section('content')
<div class="container">
    <h1 class="mb-4"><i class="fas fa-handshake"></i> Phản Hồi Yêu Cầu Nhập Hàng</h1>
    <div class="card fade-in">
        <div class="card-body">
            <form method="GET" class="mb-4 d-flex">
                <input type="text" name="search" placeholder="Tìm mã yêu cầu..." value="{{ request('search') }}" class="form-control me-2">
                <select name="status" class="form-control w-25 me-2">
                    <option value="">Tất cả trạng thái</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Đang chờ</option>
                    <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Đã chấp nhận</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Đã từ chối</option>
                </select>
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Tìm</button>
            </form>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Mã yêu cầu</th>
                        <th>Chi tiết</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $request)
                    <tr>
                        <td>{{ $request->request_number }}</td>
                        <td>{{ $request->details }}</td>
                        <td>{{ $request->status }}</td>
                        <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            @if($request->status == 'pending')
                            <form action="{{ route('supplier.requests.process', $request->id) }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="status" value="accepted">
                                <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-check"></i> Chấp nhận</button>
                            </form>
                            <form action="{{ route('supplier.requests.process', $request->id) }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-times"></i> Từ chối</button>
                            </form>
                            @else
                            <span class="text-muted">Đã xử lý</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center">Chưa có yêu cầu</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $requests->links() }}
        </div>
    </div>
</div>
@endsection
