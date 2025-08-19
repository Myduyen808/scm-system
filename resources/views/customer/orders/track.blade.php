    @extends('layouts.app')

    @section('title', 'Theo Dõi Giao Hàng - Khách Hàng')

    @section('content')
    <div class="container">
        <h1 class="mb-4"><i class="fas fa-truck"></i> Theo Dõi Giao Hàng</h1>
        <div class="card fade-in">
            <div class="card-body">
                <form method="GET" class="mb-4">
                    <input type="text" name="order_number" placeholder="Nhập mã đơn hàng..." value="{{ request()->input('order_number') }}" class="form-control w-50">
                    <button type="submit" class="btn btn-primary mt-2"><i class="fas fa-search"></i> Theo dõi</button>
                </form>
                @if($order)
                    <p><strong>Mã đơn hàng:</strong> {{ $order->order_number }}</p>
                    <p><strong>Trạng thái:</strong> {{ $order->status }}</p>
                    <p><strong>Cập nhật gần nhất:</strong> {{ $order->updated_at->format('d/m/Y H:i') }}</p>
                    <div class="progress mt-3">
                        <div class="progress-bar" role="progressbar" style="width: {{ $order->status == 'completed' ? '100%' : ($order->status == 'processing' ? '50%' : '25%') }}" aria-valuenow="{{ $order->status == 'completed' ? 100 : ($order->status == 'processing' ? 50 : 25) }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <small class="text-muted">25%: Đang chờ, 50%: Đang xử lý, 100%: Hoàn thành</small>
                @else
                    <p class="text-center">Không tìm thấy đơn hàng.</p>
                @endif
            </div>
        </div>
    </div>
    @endsection
