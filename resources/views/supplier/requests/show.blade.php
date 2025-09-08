@extends('layouts.app')

@section('title', 'Chi Tiết Yêu Cầu - Nhà Cung Cấp')

@section('content')
<div class="container">
    <h1 class="mb-4"><i class="fas fa-info-circle"></i> Chi Tiết Yêu Cầu #{{ $request->request_number ?? $request->id }}</h1>

    <div class="card fade-in">
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <p><strong>Hình ảnh:</strong>
                @if($request->product?->image)
                    <img src="{{ asset('storage/' . $request->product->image) }}" alt="{{ $request->product->name }}" style="max-width: 100px; max-height: 100px;">
                @else
                    <span>Không có hình ảnh</span>
                @endif
            </p>
            <p><strong>Sản phẩm:</strong> {{ $request->product?->name ?? 'Không xác định' }}</p>
            <p><strong>Số lượng:</strong> {{ $request->quantity ?? 0 }}</p>
            <p><strong>Trạng thái:</strong> {{ ucfirst($request->status) }}</p>
            <p><strong>Ngày tạo:</strong> {{ $request->created_at?->format('d/m/Y H:i') ?? 'Chưa có' }}</p>
            <p><strong>Ghi chú từ nhân viên:</strong> {{ $request->employee_note ?? 'Chưa có' }}</p>

            {{-- Lịch sử chat --}}
            <h5 class="mt-4">Lịch sử phản hồi</h5>
            @if ($request->replies->count() > 0)
                @foreach($request->replies as $reply)
                    <div class="alert alert-{{ $reply->user_id === Auth::id() ? 'primary' : 'success' }} mb-2">
                        <strong>{{ $reply->user_id === Auth::id() ? 'Bạn:' : 'Nhân viên:' }}</strong> {{ $reply->message }}
                        <br>
                        <small>{{ $reply->created_at->format('d/m/Y H:i') }}</small>
                    </div>
                @endforeach
            @else
                <div class="alert alert-info">Chưa có phản hồi nào.</div>
            @endif

            {{-- Form xử lý yêu cầu (nếu pending) --}}
            @if($request->status == 'pending')
                <form action="{{ route('supplier.requests.process', $request->id) }}" method="POST" class="mt-3">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Trạng thái:</label>
                        <select name="status" class="form-control" required>
                            <option value="accepted">Chấp nhận</option>
                            <option value="rejected">Từ chối</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ghi chú (nếu từ chối):</label>
                        <textarea name="note" class="form-control" placeholder="Nhập lý do từ chối" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">Xử lý</button>
                </form>
            @else
                <p><strong>Phản hồi từ nhà cung cấp:</strong> {{ $request->note_from_supplier ?? 'Chưa có' }}</p>
            @endif

            {{-- Form gửi phản hồi (nếu không closed) --}}
            @if (!in_array($request->status, ['closed']))
                <div class="mt-4">
                    <h6>Gửi phản hồi thêm</h6>
                    <form action="{{ route('supplier.requests.reply', $request->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="message" class="form-label">Phản hồi:</label>
                            <textarea name="message" id="message" class="form-control" rows="3" placeholder="Nhập phản hồi cho nhân viên" required></textarea>
                            <div class="form-text">Tối đa 1000 ký tự</div>
                        </div>
                        <button type="submit" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-paper-plane"></i> Gửi phản hồi
                        </button>
                    </form>
                </div>
            @else
                <div class="alert alert-warning">Yêu cầu đã đóng, không thể gửi phản hồi thêm.</div>
            @endif

            <a href="{{ route('supplier.requests') }}" class="btn btn-secondary mt-3"><i class="fas fa-arrow-left"></i> Quay lại</a>
        </div>
    </div>
</div>
@endsection
