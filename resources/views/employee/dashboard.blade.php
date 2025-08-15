@extends('layouts.app')

@section('title', 'Employee Dashboard')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4">
                <i class="fas fa-user-tie"></i> Employee Dashboard
            </h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <i class="fas fa-warehouse fa-3x text-primary mb-3"></i>
                    <h5 class="card-title">Quản lý kho</h5>
                    <p class="card-text">Cập nhật số lượng, nhập kho, xuất kho</p>
                    <a href="{{ route('employee.inventory') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-right"></i> Vào kho hàng
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card border-success">
                <div class="card-body text-center">
                    <i class="fas fa-tasks fa-3x text-success mb-3"></i>
                    <h5 class="card-title">Xử lý đơn hàng</h5>
                    <p class="card-text">Duyệt đơn hàng, cập nhật trạng thái</p>
                    <a href="{{ route('employee.orders') }}" class="btn btn-success">
                        <i class="fas fa-arrow-right"></i> Xử lý đơn hàng
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card border-info">
                <div class="card-body text-center">
                    <i class="fas fa-headset fa-3x text-info mb-3"></i>
                    <h5 class="card-title">Hỗ trợ khách hàng</h5>
                    <p class="card-text">Trả lời câu hỏi và xử lý khiếu nại</p>
                    <a href="{{ route('employee.support') }}" class="btn btn-info">
                        <i class="fas fa-arrow-right"></i> Hỗ trợ khách hàng
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
