@extends('layouts.app')

@section('title', 'Quản Lý Kho')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="mb-0">
                    <i class="fas fa-warehouse text-primary"></i> Quản Lý Kho
                </h1>
                <div class="btn-group">
                <a href="{{ route('admin.inventory.forecast') }}" class="btn btn-info">
                    <i class="fas fa-chart-line"></i> Dự báo tồn kho
                </a>
                <a href="{{ route('admin.inventory.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Thêm Sản Phẩm
                </a>
            </div>
        </div>
    </div>
</div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.inventory') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <input type="text"
                               name="search"
                               class="form-control"
                               placeholder="Tìm kiếm sản phẩm..."
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">Tất cả trạng thái</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Hoạt động</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tạm ngưng</option>
                            <option value="low_stock" {{ request('status') == 'low_stock' ? 'selected' : '' }}>Sắp hết hàng</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="supplier" class="form-select">
                            <option value="">Tất cả nhà cung cấp</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ request('supplier') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-search"></i> Lọc
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('admin.inventory') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-redo"></i> Làm mới
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Import Excel Form -->
    <form action="{{ route('admin.inventory.import') }}" method="POST" enctype="multipart/form-data" class="mb-4">
        @csrf
        <div class="input-group">
            <input type="file" name="file" class="form-control" accept=".xlsx,.xls" required>
            <button type="submit" class="btn btn-primary">Nhập dữ liệu hàng loạt</button>
        </div>
    </form>

    <!-- Products Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th width="80">Ảnh</th>
                            <th>Tên sản phẩm</th>
                            <th>SKU</th>
                            <th>Giá</th>
                            <th>Tồn kho</th>
                            <th>Nhà cung cấp</th>
                            <th>Trạng thái</th>
                            <th width="200">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                        <tr>
                            <td>
                                <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/60' }}"
                                     alt="{{ $product->name }}"
                                     class="img-thumbnail"
                                     style="width: 60px; height: 60px; object-fit: cover;">
                            </td>
                            <td>
                                <strong>{{ $product->name }}</strong>
                                @if($product->description)
                                    <br><small class="text-muted">{{ Str::limit($product->description, 50) }}</small>
                                @endif
                            </td>
                            <td><code>{{ $product->sku }}</code></td>
                            <td>
                                <strong class="text-success">₫{{ number_format($product->current_price) }}</strong>
                                @if($product->sale_price)
                                    <br><del class="text-muted small">₫{{ number_format($product->regular_price) }}</del>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <input type="number"
                                           class="form-control form-control-sm stock-input"
                                           style="width: 80px;"
                                           value="{{ $product->stock_quantity }}"
                                           data-product-id="{{ $product->id }}"
                                           min="0">
                                    @if($product->stock_quantity < 10)
                                        <span class="badge bg-warning ms-2">
                                            <i class="fas fa-exclamation-triangle"></i> Thấp
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                {{ $product->supplier ? $product->supplier->name : 'Chưa có' }}
                            </td>
                            <td>
                                <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-danger' }}">
                                    {{ $product->is_active ? 'Hoạt động' : 'Tạm ngưng' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('admin.inventory.edit', $product) }}"
                                       class="btn btn-outline-primary"
                                       title="Chỉnh sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button"
                                            class="btn btn-outline-danger delete-product"
                                            data-product-id="{{ $product->id }}"
                                            data-product-name="{{ $product->name }}"
                                            title="Xóa">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-box-open fa-3x mb-3"></i>
                                    <p>Không có sản phẩm nào được tìm thấy.</p>
                                    <a href="{{ route('admin.inventory.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Thêm sản phẩm đầu tiên
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($products->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $products->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc muốn xóa sản phẩm <strong id="product-name"></strong>?
                <br><small class="text-warning">Hành động này không thể hoàn tác!</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form id="delete-form" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Update stock quantity
    $('.stock-input').on('change', function() {
        const productId = $(this).data('product-id');
        const newStock = $(this).val();
        const input = $(this);

        $.ajax({
            url: `/admin/inventory/${productId}/stock`,
            method: 'PATCH',
            data: {
                _token: '{{ csrf_token() }}',
                stock_quantity: newStock
            },
            success: function(response) {
                // Show success message
                showToast('success', response.message);

                // Update badge if low stock
                const badge = input.siblings('.badge');
                if (newStock < 10) {
                    if (!badge.length) {
                        input.after('<span class="badge bg-warning ms-2"><i class="fas fa-exclamation-triangle"></i> Thấp</span>');
                    }
                } else {
                    badge.remove();
                }
            },
            error: function() {
                showToast('error', 'Có lỗi xảy ra khi cập nhật tồn kho!');
                input.val(input.data('original-value')); // Revert value
            }
        });
    });

    // Store original values
    $('.stock-input').each(function() {
        $(this).data('original-value', $(this).val());
    });

    // Delete product
    $('.delete-product').on('click', function() {
        const productId = $(this).data('product-id');
        const productName = $(this).data('product-name');

        $('#product-name').text(productName);
        $('#delete-form').attr('action', `/admin/inventory/${productId}`);
        $('#deleteModal').modal('show');
    });

    // Toast notification function
    function showToast(type, message) {
        const toast = $(`
            <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-${type === 'success' ? 'check' : 'exclamation-triangle'}"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `);

        // Add toast container if not exists
        if (!$('.toast-container').length) {
            $('body').append('<div class="toast-container position-fixed top-0 end-0 p-3"></div>');
        }

        $('.toast-container').append(toast);
        toast.toast('show');

        // Auto remove after 5 seconds
        setTimeout(() => toast.remove(), 5000);
    }
});
</script>
@endsection
