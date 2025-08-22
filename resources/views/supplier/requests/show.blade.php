@extends('layouts.app')

@section('title', 'Chi Tiết Yêu Cầu - Nhà Cung Cấp')

@section('content')
<div class="container">
    <h1 class="mb-4"><i class="fas fa-info-circle"></i> Chi Tiết Yêu Cầu #{{ $request->request_number ?? $request->id }}</h1>

    <div class="card fade-in">
        <div class="card-body">
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

            <p><strong>Sản phẩm:</strong> {{ $request->product?->name ?? 'Không xác định' }}</p>
            <p><strong>Số lượng:</strong> {{ $request->quantity ?? 0 }}</p>
            <p><strong>Trạng thái:</strong> {{ ucfirst($request->status) }}</p>
            <p><strong>Ngày tạo:</strong> {{ $request->created_at?->format('d/m/Y H:i') ?? 'Chưa có' }}</p>
            <p><strong>Ghi chú từ nhân viên:</strong> {{ $request->note ?? 'Chưa có' }}</p>

            @if($request->status == 'pending')
                <form action="{{ route('supplier.requests.process', $request->id) }}" method="POST" class="mt-3">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Trạng thái:</label>
                        <select name="status" class="form-control" required>
                            <option value="accepted">Chấp nhận</option>
                            <option value="rejected">Từ chối</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ghi chú (nếu từ chối):</label>
                        <textarea name="note" class="form-control" placeholder="Nhập lý do từ chối" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">Xử lý</button>
                </form>
            @else
                <p><strong>Phản hồi từ nhà cung cấp:</strong> {{ $request->employee_feedback ?? 'Chưa có' }}</p>
            @endif

            <a href="{{ route('supplier.requests') }}" class="btn btn-secondary mt-3"><i class="fas fa-arrow-left"></i> Quay lại</a>
        </div>
    </div>
</div>
@endsection
