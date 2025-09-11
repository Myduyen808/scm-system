@extends('layouts.app')

@section('title', 'Yêu Cầu Nhập Hàng - Nhân Viên')

@section('content')
<div class="container">
    <h1 class="mb-4"><i class="fas fa-truck-loading"></i> Yêu Cầu Nhập Hàng</h1>
    <div class="card fade-in">
        <div class="card-body">

            {{-- Hiển thị flash messages --}}
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            {{-- Nút tạo yêu cầu --}}
            <div class="mb-3">
                <a href="{{ route('employee.createStockRequest') }}" class="btn btn-primary mb-3">
                    <i class="fas fa-plus"></i> Tạo yêu cầu mới
                </a>
                <a href="{{ route('employee.internalRequestForm') }}" class="btn btn-success mb-3 ml-2">
                    <i class="fas fa-users"></i> Tạo yêu cầu nội bộ
                </a>
            </div>

            {{-- Bảng danh sách yêu cầu --}}
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Mã yêu cầu</th>
                        <th>Sản phẩm</th>
                        <th>Số lượng</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $request)
                        <tr>
                            <td>{{ $request->request_number ?? $request->id }}</td>
                            <td>
                                {{ $request->product?->name ?? 'Không xác định' }}
                                @if($request->product?->image)
                                    <br>
                                    <img src="{{ asset('storage/' . $request->product->image) }}"
                                         alt="{{ $request->product->name }}"
                                         style="max-width: 50px; max-height: 50px;">
                                @endif
                            </td>
                            <td>{{ $request->quantity ?? 0 }}</td>
                            <td>{{ ucfirst($request->status) }}</td>
                            <td>{{ $request->created_at?->format('d/m/Y H:i') ?? 'Chưa có' }}</td>
                            <td>
                                <a href="{{ route('employee.requests.show', $request->id) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Chưa có yêu cầu nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Phân trang --}}
            @if($requests instanceof \Illuminate\Pagination\LengthAwarePaginator)
               {{ $requests->links() }}
                {{ $lowStockProducts->links() }}
            @endif

        </div>
    </div>
</div>
@endsection
