@extends('layouts.app')

@section('title', 'Quản lý khuyến mãi')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4">
                <i class="fas fa-tags"></i> Quản lý khuyến mãi
            </h1>
            <p class="text-muted">Danh sách và quản lý các chương trình khuyến mãi.</p>
            <a href="{{ route('admin.promotions.create') }}" class="btn btn-primary mb-3">
                <i class="fas fa-plus"></i> Thêm khuyến mãi mới
            </a>

            <!-- Bảng danh sách khuyến mãi -->
            <div class="card">
                <div class="card-body">
                    @if ($promotions->isEmpty())
                        <p class="text-center">Không có khuyến mãi nào để hiển thị.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tên khuyến mãi</th>
                                        <th>Ngày bắt đầu</th>
                                        <th>Ngày kết thúc</th>
                                        <th>Trạng thái</th>
                                        <th>Sản phẩm áp dụng</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($promotions as $promotion)
                                        <tr>
                                            <td>{{ $promotion->id }}</td>
                                            <td>{{ $promotion->name }}</td>
                                            <td>{{ $promotion->start_date->format('d/m/Y') }}</td>
                                            <td>{{ $promotion->end_date->format('d/m/Y') }}</td>
                                            <td>
                                                @if ($promotion->is_active && $promotion->start_date <= now() && $promotion->end_date >= now())
                                                    <span class="badge bg-success">Đang hoạt động</span>
                                                @elseif ($promotion->start_date > now())
                                                    <span class="badge bg-warning">Sắp diễn ra</span>
                                                @else
                                                    <span class="badge bg-secondary">Đã kết thúc</span>
                                                @endif
                                            </td>
                                            <td>
                                                @foreach ($promotion->products as $product)
                                                    {{ $product->name }}<br>
                                                @endforeach
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.promotions.edit', $promotion->id) }}" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i> Sửa
                                                </a>
                                                <form action="{{ route('admin.promotions.destroy', $promotion->id) }}" method="POST" style="display:inline;"
                                                      onsubmit="return confirm('Bạn có chắc muốn xóa khuyến mãi này?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i> Xóa
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Phân trang -->
                        <div class="d-flex justify-content-center mt-3">
                            {{ $promotions->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
