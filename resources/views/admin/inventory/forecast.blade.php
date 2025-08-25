@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Dự báo tồn kho</h1>
    <table class="table">
        <thead>
            <tr>
                <th>Sản phẩm</th>
                <th>Tồn kho hiện tại</th>
                <th>Dự báo</th>
            </tr>
        </thead>
        <tbody>
            @foreach($forecasts as $item)
            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        <img src="{{ asset('storage/' . $item['product']->image) }}" alt="{{ $item['product']->name }}" class="me-3" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                        <span>{{ $item['product']->name }}</span>
                    </div>
                </td>
                <td>{{ $item['product']->stock_quantity }}</td>
                <td>{{ round($item['forecast']) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
