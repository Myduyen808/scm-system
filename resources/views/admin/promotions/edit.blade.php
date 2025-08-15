@extends('layouts.app')

@section('title', 'Chỉnh sửa khuyến mãi')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4">
                <i class="fas fa-edit"></i> Chỉnh sửa khuyến mãi
            </h1>
            <p class="text-muted">Cập nhật thông tin cho khuyến mãi "{{ $promotion->name }}".</p>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('admin.promotions.update', $promotion->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card">
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Tên khuyến mãi <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $promotion->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="start_date" class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" id="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date', $promotion->start_date->format('Y-m-d')) }}" required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="end_date" class="form-label">Ngày kết thúc <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" id="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date', $promotion->end_date->format('Y-m-d')) }}" required>
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="discount" class="form-label">Giảm giá (%) <span class="text-danger">*</span></label>
                            <input type="number" name="discount" id="discount" class="form-control @error('discount') is-invalid @enderror" value="{{ old('discount', $promotion->discount) }}" step="0.01" min="0" max="100" required>
                            @error('discount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="is_active" class="form-label">Trạng thái</label>
                            <select name="is_active" id="is_active" class="form-control">
                                <option value="1" {{ old('is_active', $promotion->is_active) == 1 ? 'selected' : '' }}>Kích hoạt</option>
                                <option value="0" {{ old('is_active', $promotion->is_active) == 0 ? 'selected' : '' }}>Tắt</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Cập nhật khuyến mãi</button>
                        <a href="{{ route('admin.promotions') }}" class="btn btn-secondary">Hủy</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
