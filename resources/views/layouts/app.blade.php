<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SCM System - Quản Lý Chuỗi Cung Ứng')</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #f59e0b;
            --secondary-color: #d97706;
            --accent-color: #fbbf24;
            --dark-color: #1f2937;
            --light-color: #f9fafb;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--light-color);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-color) !important;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
        }

        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        }

        .hero-section {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 80px 0;
            margin-bottom: 40px;
        }

        .fade-in {
            animation: fadeInUp 0.6s ease-out forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        .fade-in:nth-child(1) { animation-delay: 0.1s; }
        .fade-in:nth-child(2) { animation-delay: 0.2s; }
        .fade-in:nth-child(3) { animation-delay: 0.3s; }

        @keyframes fadeInUp {
            to { opacity: 1; transform: translateY(0); }
        }

        .cart-badge {
            display: inline-block;
            min-width: 18px;
            padding: 0 6px;
            font-size: 12px;
            font-weight: 700;
            line-height: 18px;
            color: #fff;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            background-color: var(--secondary-color);
            border-radius: 10px;
            position: absolute;
            top: -5px;
            right: -10px;
        }

        #notification-badge {
            font-size: 0.75rem;
            min-width: 18px;
            height: 18px;
            line-height: 18px;
            background-color: #dc3545;
        }
    </style>
    @yield('styles')
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="fas fa-cube"></i> SCM System
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">Trang chủ</a>
                    </li>

                    @auth
                        @role('customer')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('customer.products') }}">Sản phẩm</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('customer.orders') }}">Đơn hàng</a>
                            </li>
                        @endrole

                        @role('supplier')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('supplier.products') }}">Sản phẩm</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('supplier.orders') }}">Đơn hàng</a>
                            </li>
                        @endrole

                        @role('employee')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('employee.inventory') }}">Kho hàng</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('employee.orders') }}">Đơn hàng</a>
                            </li>
                        @endrole

                        @role('admin')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.inventory') }}">Kho hàng</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.orders') }}">Đơn hàng</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.reports') }}">Báo cáo</a>
                            </li>
                        @endrole
                    @endauth
                </ul>

                <ul class="navbar-nav">
                    @auth
                        <li class="nav-item position-relative">
                            @role('customer')
                                <a class="nav-link" href="{{ route('customer.cart') }}">
                                    <i class="fas fa-shopping-cart"></i>
                                    @php
                                        $cartCount = Cart::getContent()->count();
                                    @endphp
                                    @if ($cartCount > 0)
                                        <span class="cart-badge" id="cart-count">{{ $cartCount }}</span>
                                    @endif
                                </a>
                            @endrole
                        </li>

                        <li class="nav-item position-relative">
                            <a class="nav-link" href="#" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-bell"></i>
                                <span id="notification-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none;">
                                    0
                                </span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown" style="width: 350px; max-height: 400px; overflow-y: auto;">
                                <li class="dropdown-header d-flex justify-content-between align-items-center">
                                    <span>Thông báo</span>
                                    <button id="mark-all-read" class="btn btn-sm btn-link text-decoration-none p-0">
                                        Đánh dấu tất cả đã đọc
                                    </button>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <div id="notification-list">
                                    <li class="text-center p-3">
                                        <span class="text-muted">Không có thông báo mới</span>
                                    </li>
                                </div>
                            </ul>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i> {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu">
                                @role('customer')
                                    <li><a class="dropdown-item" href="{{ route('customer.orders') }}">Đơn hàng của tôi</a></li>
                                @endrole

                                @role('admin')
                                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">Trang quản trị</a></li>
                                @endrole

                                @role('employee')
                                    <li><a class="dropdown-item" href="{{ route('employee.dashboard') }}">Dashboard</a></li>
                                @endrole

                                @role('supplier')
                                    <li><a class="dropdown-item" href="{{ route('supplier.dashboard') }}">Dashboard</a></li>
                                @endrole

                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="dropdown-item" type="submit">Đăng xuất</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Đăng nhập</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Đăng ký</a>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-cube"></i> SCM System</h5>
                    <p class="mb-0">Hệ thống quản lý chuỗi cung ứng hiện đại và hiệu quả.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">&copy; 2025 SCM System. Phát triển bởi duyenb2203435@student.ctu.edu.vn {{ app()->version() }}</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Auto dismiss alerts
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);

        // Add to cart animation
        $('.add-to-cart-btn').click(function() {
            $(this).html('<i class="fas fa-spinner fa-spin"></i> Đang thêm...');
        });

        // Update cart count
        function updateCartCount(count) {
            const cartBadge = $('#cart-count');
            if (count > 0) {
                cartBadge.text(count > 99 ? '99+' : count).show();
            } else {
                cartBadge.hide();
            }
        }

        $('.add-to-cart').click(function() {
            let productId = $(this).data('id');
            $(this).html('<i class="fas fa-spinner fa-spin"></i> Đang thêm...');
            $.ajax({
                url: '/customer/cart/add/' + productId,
                type: 'POST',
                data: {
                    quantity: 1,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $(this).html('<i class="fas fa-cart-plus"></i> Thêm vào giỏ');
                    if (response.success) {
                        alert(response.message + ' Tổng số lượng: ' + response.cartCount);
                        updateCartCount(response.cartCount);
                    } else {
                        alert('Lỗi: ' + response.error);
                    }
                },
                error: function(xhr) {
                    alert('Đã xảy ra lỗi khi thêm vào giỏ hàng.');
                    $(this).html('<i class="fas fa-cart-plus"></i> Thêm vào giỏ');
                }
            }).always(function() {
                $(this).html('<i class="fas fa-cart-plus"></i> Thêm vào giỏ');
            });
        });

        // Notification System
    document.addEventListener('DOMContentLoaded', function() {
    const notificationBadge = document.getElementById('notification-badge');
    const notificationList = document.getElementById('notification-list');
    const markAllReadBtn = document.getElementById('mark-all-read');

    function loadNotifications() {
        fetch('/notifications/unread', { // Đảm bảo URL khớp với route
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(notifications => {
            updateNotificationBadge(notifications.length);
            updateNotificationList(notifications);
        })
        .catch(error => console.error('Error loading notifications:', error));
    }

    function updateNotificationBadge(count) {
        if (count > 0) {
            notificationBadge.textContent = count > 99 ? '99+' : count;
            notificationBadge.style.display = 'block';
        } else {
            notificationBadge.style.display = 'none';
        }
    }

    function updateNotificationList(notifications) {
        if (notifications.length === 0) {
            notificationList.innerHTML = `
                <li class="text-center p-3">
                    <span class="text-muted">Không có thông báo mới</span>
                </li>
            `;
        } else {
            notificationList.innerHTML = notifications.map(notification => `
                <li class="notification-item border-bottom" data-id="${notification.id}">
                    <div class="p-3 cursor-pointer" onclick="markAsRead(${notification.id})">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-1 fw-bold">${notification.title}</h6>
                                <p class="mb-1 small text-muted">${notification.message}</p>
                                <small class="text-muted">${new Date(notification.created_at).toLocaleString('vi-VN')}</small>
                            </div>
                            <div class="notification-dot bg-primary rounded-circle" style="width: 8px; height: 8px; margin-top: 8px;"></div>
                        </div>
                    </div>
                </li>
            `).join('');
        }
    }

    window.markAsRead = function(notificationId) {
        fetch(`/notifications/${notificationId}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const notificationItem = document.querySelector(`[data-id="${notificationId}"]`);
                if (notificationItem) notificationItem.remove();
                loadNotifications();
            }
        })
        .catch(error => console.error('Error marking notification as read:', error));
    };

    markAllReadBtn.addEventListener('click', function() {
        fetch('/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadNotifications();
            }
        })
        .catch(error => console.error('Error marking all as read:', error));
    });

    // Load notifications initially and every 30 seconds
    loadNotifications();
    setInterval(loadNotifications, 30000);
});
    </script>

    @yield('scripts')
</body>
</html>
