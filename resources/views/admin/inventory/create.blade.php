@extends('layouts.app')

@section('title', 'Thêm Sản Phẩm Mới')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="mb-0">
                    <i class="fas fa-plus text-primary"></i> Thêm Sản Phẩm Mới
                </h1>
                <a href="{{ route('admin.inventory') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <form action="{{ route('admin.inventory.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

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
                                    <input type="text"
                                           class="form-control @error('name') is-invalid @enderror"
                                           id="name"
                                           name="name"
                                           value="{{ old('name') }}"
                                           placeholder="Nhập tên sản phẩm"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="sku" class="form-label">Mã SKU <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control @error('sku') is-invalid @enderror"
                                           id="sku"
                                           name="sku"
                                           value="{{ old('sku') }}"
                                           placeholder="VD: PROD001"
                                           required>
                                    @error('sku')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Mô tả sản phẩm</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description"
                                      name="description"
                                      rows="4"
                                      placeholder="Nhập mô tả chi tiết về sản phẩm">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="regular_price" class="form-label">Giá gốc (VND) <span class="text-danger">*</span></label>
                                    <input type="number"
                                           class="form-control @error('regular_price') is-invalid @enderror"
                                           id="regular_price"
                                           name="regular_price"
                                           value="{{ old('regular_price') }}"
                                           min="0"
                                           step="1000"
                                           required>
                                    @error('regular_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="sale_price" class="form-label">Giá khuyến mãi (VND)</label>
                                    <input type="number"
                                           class="form-control @error('sale_price') is-invalid @enderror"
                                           id="sale_price"
                                           name="sale_price"
                                           value="{{ old('sale_price') }}"
                                           min="0"
                                           step="1000">
                                    <small class="text-muted">Để trống nếu không có khuyến mãi</small>
                                    @error('sale_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="stock_quantity" class="form-label">Số lượng tồn kho <span class="text-danger">*</span></label>
                                    <input type="number"
                                           class="form-control @error('stock_quantity') is-invalid @enderror"
                                           id="stock_quantity"
                                           name="stock_quantity"
                                           value="{{ old('stock_quantity', 0) }}"
                                           min="0"
                                           required>
                                    @error('stock_quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="supplier_id" class="form-label">Nhà cung cấp</label>
                                    <select class="form-select @error('supplier_id') is-invalid @enderror"
                                            id="supplier_id"
                                            name="supplier_id">
                                        <option value="">Chọn nhà cung cấp</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                                {{ $supplier->name }} - {{ $supplier->email }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('supplier_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check form-switch mt-4">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               id="is_active"
                                               name="is_active"
                                               value="1"
                                               {{ old('is_active', 1) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Kích hoạt sản phẩm
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Image Upload Card -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-image"></i> Hình Ảnh Sản Phẩm
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="image" class="form-label">Hình ảnh</label>
                            <input type="file"
                                   class="form-control @error('image') is-invalid @enderror"
                                   id="image"
                                   name="image"
                                   accept="image/*">
                            <small class="text-muted">Chỉ chấp nhận file: JPG, PNG, GIF. Tối đa 2MB.</small>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Image Preview -->
                        <div id="image-preview" class="mt-3" style="display: none;">
                            <img id="preview-img" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.inventory') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Hủy
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Lưu Sản Phẩm
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Sidebar với hướng dẫn -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-lightbulb text-warning"></i> Hướng Dẫn
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6><i class="fas fa-tag text-primary"></i> Mã SKU</h6>
                        <p class="small text-muted">Mã định danh duy nhất cho sản phẩm. VD: LAPTOP001, PHONE001</p>
                    </div>

                    <div class="mb-3">
                        <h6><i class="fas fa-dollar-sign text-success"></i> Giá cả</h6>
                        <p class="small text-muted">Giá khuyến mãi sẽ được hiển thị thay vì giá gốc nếu có.</p>
                    </div>

                    <div class="mb-3">
                        <h6><i class="fas fa-boxes text-info"></i> Tồn kho</h6>
                        <p class="small text-muted">Hệ thống sẽ cảnh báo khi số lượng dưới 10 sản phẩm.</p>
                    </div>

                    <div class="mb-3">
                        <h6><i class="fas fa-image text-secondary"></i> Hình ảnh</h6>
                        <p class="small text-muted">Kích thước khuyến nghị: 500x500px để có chất lượng tốt nhất.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Auto generate SKU from product name
    $('#name').on('input', function() {
        const name = $(this).val();
        if (name && !$('#sku').val()) {
            const sku = name.toUpperCase()
                           .replace(/[^A-Z0-9]/g, '')
                           .substring(0, 10) +
                           Math.random().toString(36).substring(2, 5).toUpperCase();
            $('#sku').val(sku);
        }
    });

    // Image preview
    $('#image').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#preview-img').attr('src', e.target.result);
                $('#image-preview').show();
            };
            reader.readAsDataURL(file);
        } else {
            $('#image-preview').hide();
        }
    });

    // Validate sale price
    $('#sale_price').on('input', function() {
        const regularPrice = parseFloat($('#regular_price').val()) || 0;
        const salePrice = parseFloat($(this).val()) || 0;

        if (salePrice > 0 && salePrice >= regularPrice) {
            $(this).addClass('is-invalid');
            $(this).siblings('.invalid-feedback').remove();
            $(this).after('<div class="invalid-feedback">Giá khuyến mãi phải nhỏ hơn giá gốc</div>');
        } else {
            $(this).removeClass('is-invalid');
            $(this).siblings('.invalid-feedback').remove();
        }
    });

    // Format price inputs
    $('#regular_price, #sale_price').on('input', function() {
        // Remove non-numeric characters except decimal point
        let value = $(this).val().replace(/[^\d]/g, '');
        $(this).val(value);
    });
});
</script>
@endsection
