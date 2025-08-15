@extends('layouts.app')

@section('title', 'Quản Lý Kho - Nhân Viên')

@section('content')
<div class="container">
    <h1><i class="fas fa-warehouse"></i> Quản Lý Kho</h1>
    <form method="GET" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Tìm sản phẩm..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary">Tìm</button>
        </div>
    </form>
    <a href="{{ route('employee.inventory.create') }}" class="btn btn-primary mb-3">Thêm sản phẩm</a>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Tên sản phẩm</th>
                <th>Số lượng tồn</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
            <tr>
                <td>{{ $product->name }}</td>
                <td>
                    <form action="{{ route('employee.inventory.update', $product) }}" method="POST" class="d-inline">
                        @csrf @method('PATCH')
                        <input type="number" name="stock_quantity" value="{{ $product->stock_quantity }}" class="form-control d-inline w-50" required>
                        <button type="submit" class="btn btn-sm btn-success">Cập nhật</button>
                    </form>
                </td>
                <td>
                    <a href="{{ route('employee.inventory.show', $product) }}" class="btn btn-info btn-sm">Chi tiết</a>
                    <form action="{{ route('employee.inventory.destroy', $product) }}" method="POST" onsubmit="return confirm('Bạn có chắc?');" class="d-inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="text-center">Không có sản phẩm</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    {{ $products->links() }}
</div>
@endsection
