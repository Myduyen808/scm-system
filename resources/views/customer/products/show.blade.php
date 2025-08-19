@extends('layouts.app')
@section('title', 'Chi tiết sản phẩm')
@section('content')
<div class="container">
    <h1>{{ $product->name }}</h1>
    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
    <p>Mô tả: {{ $product->description }}</p>
    <p>Giá: {{ number_format($product->current_price, 0, ',', '.') }}đ</p>
    <form action="{{ route('customer.cart.add', $product->id) }}" method="POST">
        @csrf
        <input type="number" name="quantity" value="1" min="1" max="{{ $product->inventory->stock }}">
        <button type="submit">Thêm vào giỏ</button>
    </form>
</div>
@endsection
