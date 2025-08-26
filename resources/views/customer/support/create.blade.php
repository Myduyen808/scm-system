@extends('layouts.app')

@section('title', 'Tạo Yêu Cầu Hỗ Trợ - Khách Hàng')

@section('content')
<div class="container">
    <h1 class="mb-4"><i class="fas fa-headset"></i> Tạo Yêu Cầu Hỗ Trợ</h1>
    <div class="card fade-in">
        <div class="card-body">
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <form action="{{ route('customer.storeSupport') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Tiêu đề</label>
                    <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror" required>
                    @error('subject') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Mô tả</label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4" required></textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Gửi yêu cầu</button>
                <a href="{{ route('customer.viewTickets') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
            </form>
        </div>
    </div>
</div>
@endsection
