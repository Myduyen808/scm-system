@extends('layouts.app')

@section('title', 'Chỉnh Sửa Địa Chỉ - Khách Hàng')

@section('content')
<div class="container">
    <h1 class="mb-4"><i class="fas fa-map-marker-alt"></i> Chỉnh Sửa Địa Chỉ</h1>
    <div class="card fade-in">
        <div class="card-body">
            <form action="{{ route('customer.addresses.update', $address->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Tên người nhận</label>
                    <input type="text" name="name" value="{{ old('name', $address->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Số điện thoại</label>
                    <input type="text" name="phone" value="{{ old('phone', $address->phone) }}" class="form-control @error('phone') is-invalid @enderror" required>
                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Địa chỉ</label>
                    <textarea name="address_line" class="form-control @error('address_line') is-invalid @enderror" rows="3" required>{{ old('address_line', $address->address_line) }}</textarea>
                    @error('address_line') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Đặt làm mặc định?</label>
                    <input type="checkbox" name="is_default" value="1" {{ $address->is_default ? 'checked' : '' }}>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Cập nhật</button>
                <a href="{{ route('customer.addresses.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
            </form>
        </div>
    </div>
</div>
@endsection
