@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Thống kê doanh thu</h1>

    <p>Tổng doanh thu: ₫{{ number_format($totalRevenue) }}</p>

    <table class="table">
        <thead>
            <tr>
                <th>Tháng</th>
                <th>Doanh thu</th>
            </tr>
        </thead>
        <tbody>
            @foreach($monthlyRevenue as $item)
            <tr>
                <td>{{ $item->month }}</td>
                <td>₫{{ number_format($item->revenue) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
