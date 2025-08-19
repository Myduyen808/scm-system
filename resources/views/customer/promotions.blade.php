@extends('layouts.app')

@section('title', 'Ưu Đãi & Mã Giảm Giá - Khách Hàng')

@section('content')
<div class="container">
    <h1 class="mb-4"><i class="fas fa-gift"></i> Ưu Đãi & Mã Giảm Giá</h1>
    <div class="card fade-in">
        <div class="card-body">
            @forelse($promotions as $promotion)
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">{{ $promotion->name }}</h5>
                    <p class="card-text">{{ $promotion->description }}</p>
                    <p class="card-text"><strong>Giảm giá:</strong> {{ $promotion->discount }}%</p>
                    <p class="card-text"><strong>Hết hạn:</strong> {{ $promotion->expiry_date->format('d/m/Y') }}</p>
                    <a href="#" class="btn btn-primary btn-sm">Áp dụng mã</a>
                </div>
            </div>
            @empty
            <p class="text-center">Không có ưu đãi nào hiện tại.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
