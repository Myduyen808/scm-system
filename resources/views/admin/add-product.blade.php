@extends('layouts.app')

@section('title', 'Thêm Sản Phẩm')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4">
                <i class="fas fa-plus"></i> Thêm Sản Phẩm Mới
            </h1>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.store.product') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Tên Sản Phẩm</label>
                            <input type="text" name="name" id="name" class="form-control" required>
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Mô Tả</label>
                            <textarea name="description" id="description" class="form-control" rows="3"></textarea>
                            @error('description')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="regular_price" class="form-label">Giá Chuẩn (VND)</label>
                            <input type="number" name="regular_price" id="regular_price" class="form-control" step="1000" required>
                            @error('regular_price')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="sale_price" class="form-label">Giá Khuyến Mãi (VND)</label>
                            <input type="number" name="sale_price" id="sale_price" class="form-control" step="1000">
                            @error('sale_price')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="sku" class="form-label">Mã SKU</label>
                            <input type="text" name="sku" id="sku" class="form-control" required>
                            @error('sku')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="stock_quantity" class="form-label">Số Lượng Tồn</label>
                            <input type="number" name="stock_quantity" id="stock_quantity" class="form-control" required>
                            @error('stock_quantity')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="supplier_id" class="form-label">Nhà Cung Cấp</label>
                            <select name="supplier_id" id="supplier_id" class="form-select">
                                <option value="">Chọn nhà cung cấp</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                            @error('supplier_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">Hình Ảnh</label>
                            <input type="file" name="image" id="image" class="form-control">
                            @error('image')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Thêm Sản Phẩm</button>
                        <a href="{{ route('admin.inventory') }}" class="btn btn-secondary">Quay Lại</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
