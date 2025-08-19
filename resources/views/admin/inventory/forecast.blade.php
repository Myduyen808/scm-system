@extends('layouts.app')
@section('content')
<div class="container">
    <h1>Dự báo tồn kho</h1>
    <table class="table">
        <thead><tr><th>Sản phẩm</th><th>Tồn kho hiện tại</th><th>Dự báo</th></tr></thead>
        <tbody>
            @foreach($forecasts as $item)
            <tr><td>{{ $item['product']->name }}</td><td>{{ $item['product']->stock_quantity }}</td><td>{{ round($item['forecast']) }}</td></tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
