@extends('layouts.app')

@section('title', 'Phản Hồi Yêu Cầu - Nhà Cung Cấp')

@section('content')
<div class="container">
    <h1 class="mb-4"><i class="fas fa-handshake"></i> Phản Hồi Yêu Cầu Nhập Hàng</h1>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card fade-in">
        <div class="card-body">
            <!-- Search & Filter -->
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

            <!-- Requests Table -->
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
                        <td>{{ $request->request_number ?? $request->id }}</td>
                        <td>{{ $request->product?->name ?? 'Không xác định' }}</td>
                        <td>{{ $request->quantity ?? 0 }}</td>
                        <td>{{ ucfirst($request->status) }}</td>
                        <td>{{ $request->created_at?->format('d/m/Y H:i') ?? 'Chưa có' }}</td>
                        <td>{{ $request->note ?? 'Chưa có' }}</td>
                        <td>{{ $request->employee_feedback ?? 'Chưa có' }}</td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('supplier.requests.show', $request->id) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i> Xem
                                </a>
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
                                    <span class="badge bg-secondary">Đã xử lý</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-warning">Chưa có yêu cầu nào</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination -->
            @if(method_exists($requests, 'links'))
                {{ $requests->links() }}
            @endif
        </div>
    </div>
</div>
@endsection
