<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'SCM System - Quản Lý Chuỗi Cung Ứng')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- SVG Icons -->
    <svg style="display: none;">
        <symbol id="icon_heart" viewBox="0 0 20 20">
            <g clip-path="url(#clip0_6_54)">
                <path d="M18.3932 3.30806C16.218 1.13348 12.6795 1.13348 10.5049 3.30806L9.99983 3.81285L9.49504 3.30806C7.32046 1.13319 3.78163 1.13319 1.60706 3.30806C-0.523361 5.43848 -0.537195 8.81542 1.57498 11.1634C3.50142 13.3041 9.18304 17.929 9.4241 18.1248C9.58775 18.2578 9.78467 18.3226 9.9804 18.3226C9.98688 18.3226 9.99335 18.3226 9.99953 18.3223C10.202 18.3317 10.406 18.2622 10.575 18.1248C10.816 17.929 16.4982 13.3041 18.4253 11.1631C20.5371 8.81542 20.5233 5.43848 18.3932 3.30806ZM17.1125 9.98188C15.6105 11.6505 11.4818 15.0919 9.99953 16.3131C8.51724 15.0922 4.38944 11.6511 2.88773 9.98218C1.41427 8.34448 1.40044 6.01214 2.85564 4.55693C3.59885 3.81402 4.57488 3.44227 5.5509 3.44227C6.52693 3.44227 7.50295 3.81373 8.24616 4.55693L9.3564 5.66718C9.48856 5.79934 9.65516 5.87822 9.82999 5.90589C10.1137 5.96682 10.4216 5.88764 10.6424 5.66747L11.7532 4.55693C13.2399 3.07082 15.6582 3.07111 17.144 4.55693C18.5992 6.01214 18.5854 8.34448 17.1125 9.98188Z" fill="currentColor" />
            </g>
            <defs>
                <clipPath id="clip0_6_54">
                    <rect width="20" height="20" fill="white" />
                </clipPath>
            </defs>
        </symbol>
    </svg>
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

        .favorite-btn {
            color: #ff4444;
            transition: color 0.3s ease, transform 0.3s ease;
        }

        .favorite-btn.active {
            color: #ff0000;
            transform: scale(1.2);
        }

        .favorite-btn:hover {
            color: #ff6666;
            transform: scale(1.1);
        }

        .favorite-badge {
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
            background-color: #ff4444;
            border-radius: 10px;
            position: absolute;
            top: -5px;
            right: -10px;
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

                        <!-- Thêm icon Yêu thích -->
                        <li class="nav-item position-relative">
                            @role('customer')
                                <a class="nav-link" href="{{ route('customer.favorites') }}" title="Yêu thích">
                                    <svg class="icon_heart" style="width: 20px; height: 20px;"><use href="#icon_heart"></use></svg>
                                    @php
                                        $favoriteCount = Auth::user()->favorites()->count();
                                    @endphp
                                    @if ($favoriteCount > 0)
                                        <span class="favorite-badge" id="favorite-count">{{ $favoriteCount }}</span>
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

        // Toggle Favorite
        $('.favorite-btn').click(function(e) {
            e.preventDefault();
            const productId = $(this).data('product-id');
            const isFavorite = $(this).hasClass('active');
            const $button = $(this);

            $.ajax({
                url: `/customer/favorites/toggle/${productId}`,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $button.toggleClass('active');
                        showToast(response.message, response.success ? 'success' : 'info');
                        if (response.success) {
                            fetch('/notifications/create', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    user_id: {!! json_encode(Auth::id() ?? 0) !!},
                                    type: 'favorite_added',
                                    title: 'Sản phẩm yêu thích',
                                    message: `Bạn đã yêu thích sản phẩm "${response.product_name}".`,
                                    data: { product_id: productId },
                                    related_id: productId,
                                    related_type: 'Product'
                                })
                            }).then(() => loadNotifications());
                        }
                        setTimeout(() => {
                            window.location.href = "{{ route('customer.favorites') }}";
                        }, 2000);
                    }
                },
                error: function() {
                    showToast('Có lỗi xảy ra khi cập nhật yêu thích!', 'error');
                }
            });
        });

        // Notification System
        document.addEventListener('DOMContentLoaded', function() {
            const notificationBadge = document.getElementById('notification-badge');
            const notificationList = document.getElementById('notification-list');
            const markAllReadBtn = document.getElementById('mark-all-read');

            function loadNotifications() {
                fetch('/notifications/unread', {
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

            loadNotifications();
            setInterval(loadNotifications, 30000);
        });

        // Toast notification function
        function showToast(message, type = 'success') {
            const toast = $(`
                <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0 position-fixed top-0 end-0 m-3" role="alert" style="z-index: 1050;">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="fas fa-${type === 'success' ? 'check' : 'exclamation-triangle'} me-2"></i>
                            ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            `);

            $('body').append(toast);
            toast.toast({ delay: 3000 }).toast('show');
        }
    </script>

    <!-- Botman Widget -->

    <script>
        // Đợi DOM load xong
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Khởi tạo Botman Widget...');

            // Cấu hình widget
            var botmanWidget = {
                title: 'Hỗ trợ 24/7',
                introMessage: 'Xin chào! Tôi có thể giúp gì cho bạn?',
                mainColor: '#007bff',
                bubbleBackground: '#007bff',
                aboutText: 'Chatbot Laravel SCM',
                chatServer: '/botman',
                bubbleAvatarUrl: '',
                desktopHeight: 400,
                desktopWidth: 370,
                userId: '@auth{{ Auth::id() }}@else null @endauth'
            };

            // Tải script widget
            var script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/botman-web-widget@0/build/js/widget.js';
            script.onload = function() {
                console.log('Botman widget đã tải thành công');
            };
            script.onerror = function() {
                console.error('Lỗi khi tải Botman widget');
                // Fallback: tạo bubble chat đơn giản
                createFallbackChatBubble();
            };
            document.head.appendChild(script);
        });

        // Tạo chat bubble đơn giản nếu widget không load được
        function createFallbackChatBubble() {
            console.log('Tạo fallback chat bubble');
            var bubble = document.createElement('div');
            bubble.innerHTML = `
                <div style="position: fixed; bottom: 20px; right: 20px; width: 60px; height: 60px; background: #007bff; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 1000; box-shadow: 0 4px 12px rgba(0,123,255,0.3);" onclick="openFallbackChat()">
                    <span style="color: white; font-size: 24px;">💬</span>
                </div>
            `;
            document.body.appendChild(bubble);
        }

        // Mở chat fallback
        function openFallbackChat() {
            alert('Chatbot đang được phát triển. Vui lòng liên hệ hotline: 1900-1234');
        }
    </script>


    @yield('scripts')
</body>
</html>
