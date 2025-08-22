@extends('layouts.app')

@section('title', 'Quản Lý Sản Phẩm - Nhà Cung Cấp')

@section('content')
<div class="container">
    <h1 class="mb-4"><i class="fas fa-boxes"></i> Quản Lý Sản Phẩm</h1>
    <!-- Flash Messages -->
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
    <div class="row mb-4">
        <div class="col-md-6">
            <a href="{{ route('supplier.products.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Thêm sản phẩm mới
            </a>
        </div>
        <div class="col-md-6">
            <form method="GET" class="d-flex">
                <input type="text" name="search" placeholder="Tìm tên hoặc SKU..." value="{{ request('search') }}" class="form-control me-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Tìm</button>
            </form>
        </div>
    </div>
    <div class="card fade-in">
        <div class="card-body">
            @if($products->where('is_approved', false)->count() > 0)
                <div class="alert alert-warning mb-4" role="alert">
                    Bạn có {{ $products->where('is_approved', false)->count() }} sản phẩm đang chờ phê duyệt.
                </div>
            @endif
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Ảnh</th>
                        <th>Tên</th>
                        <th>SKU</th>
                        <th>Giá</th>
                        <th>Tồn kho</th>
                        <th>Số lượng bán</th>
                        <th>Doanh thu</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td>
                            @if($product->image)
                                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" style="max-width: 100px; max-height: 100px; object-fit: cover;" class="img-fluid" onerror="this.src='https://via.placeholder.com/100x100?text=No+Image'">
                            @else
                                <img src="https://via.placeholder.com/100x100?text=No+Image" alt="{{ $product->name }}" style="max-width: 100px; max-height: 100px; object-fit: cover;" class="img-fluid">
                            @endif
                        </td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->sku }}</td>
                        <td>₫{{ number_format($product->current_price ?? 0, 0, ',', '.') }}</td>
                        <td>
                            <form action="{{ route('supplier.products.updateStock', $product->id) }}" method="POST" class="d-flex" onsubmit="return confirm('Cập nhật tồn kho?');">
                                @csrf
                                @method('PUT')
                                <input type="number" name="stock_quantity" value="{{ $product->inventory->stock ?? 0 }}" class="form-control w-50 me-2" min="0">
                                <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-sync"></i> Cập nhật</button>
                            </form>
                        </td>
                        <td>{{ $product->orderItems->sum('quantity') ?? 0 }}</td>
                        <td>₫{{ number_format(($product->orderItems->sum(function ($item) { return ($item->price ?? 0) * ($item->quantity ?? 0); }) ?? 0), 0, ',', '.') }}</td>
                        <td>{{ $product->is_approved ? 'Đã duyệt' : 'Chờ duyệt' }}</td>
                        <td>
                            <a href="{{ route('supplier.products.edit', $product->id) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Sửa</a>
                            <form action="{{ route('supplier.products.destroy', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa sản phẩm?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Xóa</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center">Chưa có sản phẩm</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection
