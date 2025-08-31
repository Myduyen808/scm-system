@extends('layouts.app')

@section('title', 'Chi Tiết Yêu Cầu - Nhân Viên')

@section('content')
<div class="container">
    <h1 class="mb-4"><i class="fas fa-info-circle"></i> Chi Tiết Yêu Cầu #{{ $request->request_number ?? $request->id }}</h1>

    <div class="card fade-in">
        <div class="card-body">
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

            <p><strong>Hình ảnh:</strong>
                @if($request->product?->image)
                    <img src="{{ asset('storage/' . $request->product->image) }}" alt="{{ $request->product->name }}" style="max-width: 100px; max-height: 100px;">
                @else
                    <span>Không có hình ảnh</span>
                @endif
            </p>
            <p><strong>Sản phẩm:</strong> {{ $request->product?->name ?? 'Không xác định' }}</p>
            <p><strong>Số lượng:</strong> {{ $request->quantity ?? 0 }}</p>
            <p><strong>Trạng thái:</strong> {{ ucfirst($request->status) }}</p>
            <p><strong>Ngày tạo:</strong> {{ $request->created_at?->format('d/m/Y H:i') ?? 'Chưa có' }}</p>
            <p><strong>Nhà cung cấp:</strong> {{ $request->supplier->name ?? 'Chưa rõ' }}</p>
            <p><strong>Ghi chú từ nhân viên:</strong> {{ $request->employee_note ?? 'Chưa có' }}</p>

            @if($request->status === 'pending')
                <form action="{{ route('employee.process.request', $request->id) }}" method="POST" class="mt-3">
                    @csrf
                    @method('PATCH')
                    <div class="mb-3">
                        <label class="form-label">Trạng thái:</label>
                        <select name="status" class="form-control" required>
                            <option value="accepted">Chấp nhận</option>
                            <option value="rejected">Từ chối</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ghi chú (nếu cần):</label>
                        <textarea name="note" class="form-control" placeholder="Nhập ghi chú" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">Xử lý</button>
                </form>
            @else
                <p><strong>Phản hồi từ nhà cung cấp:</strong> {{ $request->note_from_supplier ?? 'Chưa có' }}</p>
            @endif

            <a href="{{ route('employee.requests') }}" class="btn btn-secondary mt-3"><i class="fas fa-arrow-left"></i> Quay lại</a>
        </div>
    </div>
</div>
@endsection
