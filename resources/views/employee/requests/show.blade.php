@extends('layouts.app')

@section('title', 'Chi Tiết Yêu Cầu Nhập Hàng')

@section('content')
<div class="container">
    <h1><i class="fas fa-truck-loading"></i> Chi Tiết Yêu Cầu Nhập Hàng #{{ $request->id }}</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Thông Tin Yêu Cầu</h5>
            <p><strong>Sản phẩm:</strong> {{ $request->product->name ?? 'Chưa rõ' }}</p>
            <p><strong>Số lượng:</strong> {{ $request->quantity }}</p>
            <p><strong>Trạng thái:</strong> {{ ucfirst($request->status) }}</p>
            <p><strong>Ngày tạo:</strong> {{ $request->created_at->format('d/m/Y H:i') }}</p>
            <p><strong>Nhà cung cấp:</strong> {{ $request->supplier->name ?? 'Chưa rõ' }}</p>
            <p><strong>Ghi chú từ nhân viên:</strong> {{ $request->employee_note ?: 'Chưa có' }}</p>
            <p><strong>Phản hồi từ nhà cung cấp:</strong> {{ $request->note_from_supplier ?: 'Chưa có' }}</p>
        </div>
    </div>

    @if ($request->status === 'pending')
        <div class="mt-3">
            <form action="{{ route('employee.process.request', $request->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="mb-3">
                    <label for="status" class="form-label">Trạng thái</label>
                    <select name="status" class="form-select" required>
                        <option value="accepted">Chấp nhận</option>
                        <option value="rejected">Từ chối</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Xử lý</button>
                <a href="{{ route('employee.requests') }}" class="btn btn-secondary">Quay lại</a>
            </form>
        </div>
    @endif
</div>
@endsection
