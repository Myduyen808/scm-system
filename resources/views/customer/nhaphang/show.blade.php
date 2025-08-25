@extends('layouts.app')

@section('title', 'Chi Tiết Yêu Cầu Nhập Hàng - Nhân Viên')

@section('content')
<div class="container">
    <h1 class="mb-4"><i class="fas fa-info-circle"></i> Chi Tiết Yêu Cầu #{{ $request->id }}</h1>
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
            <p><strong>Mã yêu cầu:</strong> {{ $request->id }}</p>
            <p><strong>Sản phẩm:</strong> {{ $request->product->name ?? 'Không xác định' }}</p>
            <p><strong>Số lượng:</strong> {{ $request->quantity ?? 0 }}</p>
            <p><strong>Nhà cung cấp:</strong> {{ $request->supplier->name ?? 'Chưa rõ' }}</p>
            <p><strong>Trạng thái:</strong> {{ ucfirst($request->status) }}</p>
            <p><strong>Ngày tạo:</strong> {{ $request->created_at->format('d/m/Y H:i') ?? 'Chưa có' }}</p>
            <p><strong>Ghi chú từ nhân viên:</strong> {{ $request->note ?? 'Chưa có' }}</p>
            <p><strong>Phản hồi từ nhà quản lý:</strong>
                @if($request->employee_feedback)
                    {{ $request->employee_feedback }}
                @else
                    <span class="text-muted">Chưa có phản hồi từ nhà quản lý</span>
                @endif
            </p>
            <a href="{{ route('employee.nhaphang') }}" class="btn btn-secondary mt-3"><i class="fas fa-arrow-left"></i> Quay lại</a>
        </div>
    </div>
</div>
@endsection
