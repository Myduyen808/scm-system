@extends('layouts.app')

@section('title', 'Phản Hồi Yêu Cầu - Nhân Viên')

@section('content')
<div class="container">
    <h1><i class="fas fa-reply"></i> Phản Hồi Yêu Cầu #{{ $request->id }}</h1>
    <div class="card mb-3">
        <div class="card-body">
            <p><strong>Nhà cung cấp:</strong> {{ $request->supplier->name ?? 'Chưa rõ' }}</p>
            <p><strong>Mô tả:</strong> {{ $request->description }}</p>
            <p><strong>Trạng thái:</strong> {{ $request->status }}</p>
            <p><strong>Ngày tạo:</strong> {{ $request->created_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('employee.requests.feedback', $request) }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="status" class="form-label">Trạng thái</label>
            <select name="status" class="form-control" required>
                <option value="accepted" {{ $request->status == 'accepted' ? 'selected' : '' }}>Chấp nhận</option>
                <option value="rejected" {{ $request->status == 'rejected' ? 'selected' : '' }}>Từ chối</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="feedback" class="form-label">Phản hồi</label>
            <textarea name="feedback" class="form-control" id="feedback" rows="5" required>{{ old('feedback') }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary">Gửi phản hồi</button>
        <a href="{{ route('employee.requests') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
@endsection
