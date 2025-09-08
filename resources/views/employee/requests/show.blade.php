@extends('layouts.app')

@section('title', 'Chi Tiết Yêu Cầu - Nhân Viên')

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

            {{-- Thông tin yêu cầu --}}
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Thông tin yêu cầu</h5>
                    <p><strong>Sản phẩm:</strong> {{ $request->product?->name ?? 'Không xác định' }}</p>
                    <p><strong>Số lượng yêu cầu:</strong> {{ $request->quantity ?? 0 }}</p>
                    <p><strong>Ngày tạo:</strong> {{ $request->created_at?->format('d/m/Y H:i') ?? 'Chưa có' }}</p>
                    <p><strong>Nhà cung cấp:</strong> {{ $request->supplier->name ?? 'Chưa rõ' }}</p>
                    <p><strong>Ghi chú ban đầu:</strong> {{ $request->employee_note ?? 'Không có ghi chú' }}</p>
                </div>
                <div class="col-md-6">
                    <h5>Hình ảnh sản phẩm</h5>
                    @if($request->product?->image)
                        <img src="{{ asset('storage/' . $request->product->image) }}"
                             alt="{{ $request->product->name }}"
                             class="img-thumbnail"
                             style="max-width: 200px; max-height: 200px;">
                    @else
                        <div class="text-muted">Không có hình ảnh</div>
                    @endif
                </div>
            </div>

            {{-- Trạng thái và phản hồi --}}
            <div class="row">
                <div class="col-12">
                    <h5>Trạng thái và phản hồi</h5>

                    {{-- Hiển thị trạng thái --}}
                    <div class="mb-3">
                        <span class="badge
                            @if($request->status === 'pending') bg-warning
                            @elseif($request->status === 'accepted') bg-success
                            @elseif($request->status === 'rejected') bg-danger
                            @else bg-secondary
                            @endif fs-6">
                            @if($request->status === 'pending') Đang chờ xử lý
                            @elseif($request->status === 'accepted') Đã chấp nhận
                            @elseif($request->status === 'rejected') Đã từ chối
                            @else {{ ucfirst($request->status) }}
                            @endif
                        </span>
                    </div>

                    {{-- Lịch sử chat --}}
                    <h6>Lịch sử phản hồi</h6>
                    @if ($request->replies->count() > 0)
                        @foreach($request->replies as $reply)
                            <div class="alert alert-{{ $reply->user_id === Auth::id() ? 'primary' : 'success' }} mb-2">
                                <strong>{{ $reply->user_id === Auth::id() ? 'Bạn:' : 'Nhà cung cấp:' }}</strong> {{ $reply->message }}
                                <br>
                                <small>{{ $reply->created_at->format('d/m/Y H:i') }}</small>
                            </div>
                        @endforeach
                    @else
                        <div class="alert alert-info">Chưa có phản hồi nào.</div>
                    @endif

                    {{-- Phản hồi từ nhà cung cấp (nếu có) --}}
                    @if($request->status !== 'pending')
                        <div class="alert alert-info mt-3">
                            <h6><i class="fas fa-reply"></i> Phản hồi từ nhà cung cấp:</h6>
                            <p class="mb-0">{{ $request->note_from_supplier ?? 'Nhà cung cấp chưa để lại ghi chú.' }}</p>
                            <small class="text-muted">
                                Cập nhật lúc: {{ $request->updated_at?->format('d/m/Y H:i') ?? 'Chưa rõ' }}
                            </small>
                        </div>
                    @else
                        <div class="alert alert-warning mt-3">
                            <i class="fas fa-clock"></i> Yêu cầu đang được chờ nhà cung cấp xử lý...
                        </div>
                    @endif

                    {{-- Form phản hồi thêm cho nhà cung cấp (nếu cần) --}}
                    @if (!in_array($request->status, ['closed']))
                        <div class="mt-4">
                            <h6>Phản hồi thêm cho nhà cung cấp</h6>
                            <form action="{{ route('employee.requests.reply', $request->id) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="message" class="form-label">Ghi chú phản hồi:</label>
                                    <textarea name="message" id="message" class="form-control" rows="3" placeholder="Nhập phản hồi cho nhà cung cấp" required></textarea>
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
                </div>
            </div>

            {{-- Nút quay lại --}}
            <div class="mt-4">
                <a href="{{ route('employee.requests') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại danh sách
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
