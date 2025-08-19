@extends('layouts.app')

@section('title', 'Danh Sách Địa Chỉ - Khách Hàng')

@section('content')
<div class="container">
    <h1 class="mb-4"><i class="fas fa-map-marker-alt"></i> Danh Sách Địa Chỉ</h1>
    <div class="card fade-in">
        <div class="card-body">
            <a href="{{ route('customer.addresses.create') }}" class="btn btn-primary mb-3"><i class="fas fa-plus"></i> Thêm Địa Chỉ Mới</a>
            @if ($addresses->isEmpty())
                <p class="text-center">Bạn chưa có địa chỉ nào. <a href="{{ route('customer.addresses.create') }}">Thêm ngay!</a></p>
            @else
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Tên</th>
                            <th>Số điện thoại</th>
                            <th>Địa chỉ</th>
                            <th>Mặc định</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($addresses as $address)
                            <tr>
                                <td>{{ $address->name }}</td>
                                <td>{{ $address->phone }}</td>
                                <td>{{ $address->address_line }}</td>
                                <td>{{ $address->is_default ? 'Có' : 'Không' }}</td>
                                <td>
                                    <a href="{{ route('customer.addresses.update', $address->id) }}" class="btn btn-sm btn-info"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('customer.addresses.delete', $address->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xóa?')"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
@endsection
