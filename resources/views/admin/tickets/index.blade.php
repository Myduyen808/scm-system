@extends('layouts.app')

@section('title', 'Quản Lý Ticket - Admin')

@section('content')
<div class="container mt-4">
    <div class="row">
        <h1 class="mb-4"><i class="fas fa-ticket-alt"></i> Quản Lý Ticket</h1>
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @foreach($tickets as $ticket)
            <div class="col-12 col-md-6 col-lg-4 mb-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body d-flex align-items-center justify-content-between p-3">
                        <div>
                            <h5 class="card-title mb-1">{{ $ticket->subject }}</h5>
                            <p class="card-text text-muted small">
                                Trạng thái: <span class="badge bg-{{ $ticket->status == 'open' ? 'warning' : ($ticket->status == 'assigned' ? 'info' : 'success') }}">{{ $ticket->status }}</span>
                            </p>
                        </div>
                        @if($ticket->status == 'open')
                            <form action="{{ route('admin.tickets.assign', $ticket->id) }}" method="POST" class="d-inline-flex align-items-center">
                                @csrf
                                <select name="assigned_to" class="form-select me-2" style="width: 150px;">
                                    @foreach(\App\Models\User::role('employee')->get() as $employee)
                                        <option value="{{ $employee->id }}" {{ $ticket->assigned_to == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-primary btn-sm">Phân công</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
        @if($tickets->isEmpty())
            <div class="col-12 text-center">Không có ticket nào để phân công.</div>
        @endif
    </div>
</div>
@endsection
