@extends('layouts.app')

@section('title', 'Phản Hồi Yêu Cầu Nhập Hàng')

@section('content')
<div class="container">
    <h1><i class="fas fa-truck-loading"></i> Phản Hồi Yêu Cầu Nhập Hàng</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Filters -->
    <div class="mb-3">
        <form method="GET" action="{{ route('employee.requests') }}" class="d-flex">
            <input type="text" name="search" class="form-control me-2" placeholder="Tìm mã yêu cầu..." value="{{ request('search') }}">
            <select name="status" class="form-select me-2">
                <option value="">Tất cả trạng thái</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Đã chấp nhận</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Đã từ chối</option>
            </select>
            <button type="submit" class="btn btn-outline-primary">Lọc</button>
        </form>
    </div>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Mã yêu cầu</th>
                    <th>Sản phẩm</th>
                    <th>Số lượng</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                    <th>Ghi chú từ nhân viên</th>
                    <th>Phản hồi từ nhà cung cấp</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $request)
                    <tr>
                        <td>{{ $request->id }}</td>
                        <td>{{ $request->product->name ?? 'Chưa rõ' }}</td>
                        <td>{{ $request->quantity }}</td>
                        <td>{{ ucfirst($request->status) }}</td>
                        <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $request->employee_note ?: 'Chưa có' }}</td>
                        <td>{{ $request->note_from_supplier ?? 'Chưa có' }}</td>
                        <td>
                            <a href="{{ route('employee.requests.show', $request->id) }}" class="btn btn-info btn-sm">Xem Chi Tiết</a>
                            @if ($request->status === 'pending')
                                <form action="{{ route('employee.process.request', $request->id) }}" method="POST" style="display:inline; margin-left: 5px;">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="form-select form-select-sm d-inline-block w-auto">
                                        <option value="accepted">Chấp nhận</option>
                                        <option value="rejected">Từ chối</option>
                                    </select>
                                    <button type="submit" class="btn btn-primary btn-sm" style="margin-left: 5px;">Xử lý</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">Không có yêu cầu nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $requests->appends(request()->query())->links() }}
</div>
@endsection
