@extends('layouts.app')

@section('title', 'Thông Báo')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4"><i class="fas fa-bell"></i> Thông Báo</h1>

            @if($notifications->count() > 0)
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Danh sách thông báo</h5>
                        <button id="mark-all-read-page" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-check-double"></i> Đánh dấu tất cả đã đọc
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @foreach($notifications as $notification)
                                <div class="list-group-item notification-item {{ !$notification->is_read ? 'bg-light' : '' }}"
                                     data-id="{{ $notification->id }}">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-1">
                                                <h6 class="mb-0 fw-bold">{{ $notification->title }}</h6>
                                                @if(!$notification->is_read)
                                                    <span class="badge bg-primary ms-2">Mới</span>
                                                @endif
                                            </div>
                                            <p class="mb-1 text-muted">{{ $notification->message }}</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">
                                                    <i class="fas fa-clock"></i>
                                                    {{ $notification->created_at->diffForHumans() }}
                                                </small>
                                                @if(!$notification->is_read)
                                                    <button class="btn btn-sm btn-outline-success mark-read-btn"
                                                            data-id="{{ $notification->id }}">
                                                        <i class="fas fa-check"></i> Đánh dấu đã đọc
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Không có thông báo nào</h5>
                        <p class="text-muted">Bạn sẽ nhận được thông báo ở đây khi có cập nhật mới.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.mark-read-btn').forEach(button => {
        button.addEventListener('click', function() {
            const notificationId = this.getAttribute('data-id');
            markAsRead(notificationId, this);
        });
    });

    document.getElementById('mark-all-read-page')?.addEventListener('click', function() {
        markAllAsRead();
    });

    function markAsRead(notificationId, buttonElement) {
        fetch(`/notifications/${notificationId}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const notificationItem = buttonElement.closest('.notification-item');
                notificationItem.classList.remove('bg-light');
                const newBadge = notificationItem.querySelector('.badge.bg-primary');
                if (newBadge) newBadge.remove();
                buttonElement.remove();
            }
        })
        .catch(error => {
            console.error('Error marking notification as read:', error);
            alert('Có lỗi xảy ra. Vui lòng thử lại.');
        });
    }

    function markAllAsRead() {
        fetch('/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error marking all as read:', error);
            alert('Có lỗi xảy ra. Vui lòng thử lại.');
        });
    }
});
</script>
@endpush

@push('styles')
<style>
.notification-item {
    transition: all 0.3s ease;
}

.notification-item:hover {
    background-color: #f8f9fa !important;
}

.notification-item.bg-light {
    border-left: 4px solid #007bff;
}
</style>
@endpush
@endsection
