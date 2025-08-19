@extends('layouts.app')

@section('title', 'Sản phẩm chờ phê duyệt - SCM System')

@section('content')
<div class="container py-5">
    <h2 class="fw-bold mb-4">Sản phẩm chờ phê duyệt</h2>

    @if($pendingProducts->isEmpty())
        <p>Không có sản phẩm nào chờ phê duyệt.</p>
    @else
        <div class="row">
            @foreach($pendingProducts as $product)
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        @if($product->image)
                            <img src="{{ Storage::url($product->image) }}" class="card-img-top" alt="{{ $product->name }}" style="max-height: 200px; object-fit: cover;" onerror="this.src='https://via.placeholder.com/300x200?text=No+Image'">
                        @else
                            <img src="https://via.placeholder.com/300x200?text=No+Image" class="card-img-top" alt="{{ $product->name }}" style="max-height: 200px; object-fit: cover;">
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $product->name }}</h5>
                            <p class="card-text">SKU: {{ $product->sku }}</p>
                            <p class="card-text">Giá: {{ number_format($product->regular_price, 0, ',', '.') }}đ</p>
                            <p class="card-text">Tồn kho: {{ $product->inventory ? $product->inventory->stock : 0 }}</p>
                            <p class="card-text">Nhà cung cấp: {{ $product->supplier ? $product->supplier->name : 'Chưa rõ' }}</p>
                            <form action="{{ route('employee.products.approve', $product->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-primary">Phê duyệt</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
