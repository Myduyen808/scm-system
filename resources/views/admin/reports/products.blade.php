@extends('layouts.app')

@section('title', 'Báo Cáo Sản Phẩm')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="mb-0">
                    <i class="fas fa-boxes text-primary"></i> Báo Cáo Sản Phẩm
                </h1>
                <div>
                    <a href="{{ route('admin.reports.export.products') }}" class="btn btn-success me-2">
                        <i class="fas fa-download"></i> Xuất CSV
                    </a>
                    <a href="{{ route('admin.reports') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Tên sản phẩm</th>
                                    <th>Số lượng bán</th>
                                    <th>SKU</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topProducts as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->order_items_count }}</td>
                                    <td>{{ $product->sku }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Không có dữ liệu</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $topProducts->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
