@extends('layouts.app')

@section('title', 'Danh Sách Địa Chỉ - Khách Hàng')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 text-center mb-4">
            <h1 class="display-5 fw-bold text-primary mt-3"><i class="fas fa-map-marker-alt"></i> Danh Sách Địa Chỉ</h1>
            <p class="text-muted">Quản lý các địa chỉ giao hàng của bạn</p>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('customer.addresses.create') }}" class="btn btn-primary mb-3"><i class="fas fa-plus"></i> Thêm Địa Chỉ Mới</a>
                    @if ($addresses->isEmpty())
                        <p class="text-center">Bạn chưa có địa chỉ nào. <a href="{{ route('customer.addresses.create') }}" class="btn btn-link">Thêm ngay!</a></p>
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
                                            <a href="{{ route('customer.addresses.edit', $address->id) }}" class="btn btn-sm btn-info"><i class="fas fa-edit"></i></a>
                                            <form action="{{ route('customer.addresses.delete', $address->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc muốn xóa địa chỉ này?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('customer.cart') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại giỏ hàng
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
