@extends('layouts.app')
@section('title', 'Chỉnh Sửa Sản Phẩm')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="mb-0">
                    <i class="fas fa-edit text-primary"></i> Chỉnh Sửa Sản Phẩm
                </h1>
                <a href="{{ route('admin.inventory') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8">
            <form action="{{ route('admin.inventory.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle"></i> Thông Tin Sản Phẩm
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $product->name) }}" required>
                                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="sku" class="form-label">Mã SKU <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('sku') is-invalid @enderror" id="sku" name="sku" value="{{ old('sku', $product->sku) }}" required>
                                    @error('sku') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                        <!-- Thêm các field khác tương tự create.blade.php: description, regular_price, sale_price, stock_quantity, supplier_id, is_active -->
                        <!-- Ví dụ field description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Mô tả sản phẩm</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description', $product->description) }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <!-- ... Thêm các field còn lại giống create.blade.php, chỉ thay value bằng old hoặc $product->field ... -->

                        <!-- Image section -->
                        <div class="card mt-3">
                            <div class="card-header"><h5><i class="fas fa-image"></i> Hình Ảnh</h5></div>
                            <div class="card-body">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="Current Image" class="img-thumbnail mb-3" style="max-width: 200px;">
                                @endif
                                <input type="file" class="form-control @error('image') is-invalid @enderror" name="image" accept="image/*">
                                @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mt-3 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Cập Nhật</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <!-- Sidebar hướng dẫn giống create.blade.php -->
    </div>
</div>
@endsection
