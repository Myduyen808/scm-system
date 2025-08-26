@extends('layouts.app')

@section('title', 'Quản lý Ticket - Admin')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4"><i class="fas fa-ticket-alt"></i> Quản lý Ticket</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if ($ticketToAssign)
                        <form action="{{ route('admin.tickets.assign', ['id' => $ticketToAssign->id]) }}" method="POST">
                            @csrf
                            <div class="col-md-4 mb-4">
                                <div class="card h-100 border-danger">
                                    <div class="card-body text-center">
                                        <i class="fas fa-ticket-alt fa-3x text-danger mb-3"></i>
                                        <h5 class="card-title">Phân công ticket</h5>
                                        <p class="card-text">Phân công ticket cho nhân viên</p>
                                        <select name="assigned_to" class="form-control mb-2" required>
                                            @foreach($employees as $employee)
                                                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-danger btn-block">
                                            <i class="fas fa-arrow-right"></i> Phân công
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    @else
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 border-secondary">
                                <div class="card-body text-center">
                                    <i class="fas fa-ticket-alt fa-3x text-secondary mb-3"></i>
                                    <h5 class="card-title">Quản lý ticket</h5>
                                    <p class="card-text">Hiện không có ticket nào để phân công</p>
                                    <button class="btn btn-secondary btn-block" disabled>Chưa có ticket</button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
