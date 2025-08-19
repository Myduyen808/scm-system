@extends('layouts.app')

@section('title', 'Chi Tiết Yêu Cầu Hỗ Trợ - Khách Hàng')

@section('content')
<div class="container">
    <h1 class="mb-4"><i class="fas fa-headset"></i> Chi Tiết Yêu Cầu #{{ $ticket->ticket_number }}</h1>
    <div class="card fade-in">
        <div class="card-body">
            <p><strong>Tiêu đề:</strong> {{ $ticket->title }}</p>
            <p><strong>Mô tả:</strong> {{ $ticket->description }}</p>
            <p><strong>Trạng thái:</strong> {{ $ticket->status }}</p>
            <p><strong>Ngày tạo:</strong> {{ $ticket->created_at->format('d/m/Y H:i') }}</p>
            <a href="{{ route('customer.support.index') }}" class="btn btn-secondary mt-3"><i class="fas fa-arrow-left"></i> Quay lại</a>
        </div>
    </div>
</div>
@endsection
