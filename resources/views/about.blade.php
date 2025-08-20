@extends('layouts.app')

@section('title', 'Về Chúng Tôi - SCM System')

@section('content')
<!-- Hero Section -->
<section class="hero-section bg-secondary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 fade-in">
                <h1 class="display-4 fw-bold mb-4">Về Chúng Tôi</h1>
                <p class="lead mb-4">Chào mừng bạn đến với Hệ Thống Quản Lý Chuỗi Cung Ứng (SCM System) - Giải pháp tối ưu cho việc quản lý và vận hành chuỗi cung ứng hiệu quả.</p>
                <div class="d-flex gap-3">
                    <a href="{{ route('customer.products') }}" class="btn btn-light btn-lg">
                        <i class="fas fa-shopping-bag me-2"></i>Bắt đầu mua sắm
                    </a>
                    <a href="#mission" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-info-circle me-2"></i>Tìm hiểu thêm
                    </a>
                </div>
            </div>
            <div class="col-lg-6 fade-in">
                <div class="text-center">
                    <i class="fas fa-users" style="font-size: 200px; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mission Section -->
<section id="mission" class="py-5">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-12">
                <h2 class="fw-bold mb-3">Sứ Mệnh Của Chúng Tôi</h2>
                <p class="lead text-muted">Chúng tôi cam kết mang đến giải pháp quản lý chuỗi cung ứng tiên tiến, giúp doanh nghiệp tối ưu hóa quy trình và nâng cao trải nghiệm khách hàng.</p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-4 fade-in">
                <div class="card h-100 text-center p-4">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fas fa-cogs text-primary" style="font-size: 3rem;"></i>
                        </div>
                        <h5 class="card-title">Công Nghệ Hiện Đại</h5>
                        <p class="card-text text-muted">Sử dụng công nghệ tiên tiến để tự động hóa và tối ưu hóa các quy trình quản lý.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4 fade-in">
                <div class="card h-100 text-center p-4">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fas fa-shield-alt text-primary" style="font-size: 3rem;"></i>
                        </div>
                        <h5 class="card-title">Bảo Mật Tuyệt Đối</h5>
                        <p class="card-text text-muted">Đảm bảo an toàn dữ liệu với các tiêu chuẩn bảo mật cao nhất.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4 fade-in">
                <div class="card h-100 text-center p-4">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fas fa-users text-primary" style="font-size: 3rem;"></i>
                        </div>
                        <h5 class="card-title">Hỗ Trợ Tận Tâm</h5>
                        <p class="card-text text-muted">Đội ngũ hỗ trợ 24/7 sẵn sàng giúp bạn mọi lúc, mọi nơi.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Our Team Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-12">
                <h2 class="fw-bold mb-3">Đội Ngũ Của Chúng Tôi</h2>
                <p class="lead text-muted">Đội ngũ chuyên nghiệp với kinh nghiệm trong lĩnh vực chuỗi cung ứng và công nghệ.</p>
            </div>
        </div>

<!-- Founder: Mỹ Duyên (Trên Trần Thị B, căn giữa) -->
        <div class="row mb-5 justify-content-center">
            <div class="col-md-4 mb-4 fade-in">
                <div class="card h-100 text-center p-4">
                    <div class="card-body">
                        <img src="{{ asset('assets/images/about/duyen.png') }}" alt="Mỹ Duyên" class="rounded-circle mb-3" style="width: 150px; height: 150px;">
                        <h5 class="card-title">Mỹ Duyên</h5>
                        <p class="card-text text-muted">Founder</p>
                        <div style="font-size: 18px;">
                            <a href="https://www.facebook.com/my.duyen.79248" target="_blank" style="margin-right: 10px; color: #000;"><i class="fab fa-facebook"></i></a>
                            <a href="https://www.instagram.com/your_instagram" target="_blank" style="margin-right: 10px; color: #000;"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Nhân viên A, B, C (Dưới Founder) -->
        <div class="row">
            <div class="col-md-4 mb-4 fade-in">
                <div class="card h-100 text-center p-4">
                    <div class="card-body">
                        <img src="https://via.placeholder.com/150?text=Nhân viên+1" alt="Nhân viên 1" class="rounded-circle mb-3" style="width: 150px; height: 150px;">
                        <h5 class="card-title">Nguyễn Văn A</h5>
                        <p class="card-text text-muted">Giám đốc Công nghệ</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4 fade-in">
                <div class="card h-100 text-center p-4">
                    <div class="card-body">
                        <img src="https://via.placeholder.com/150?text=Nhân viên+2" alt="Nhân viên 2" class="rounded-circle mb-3" style="width: 150px; height: 150px;">
                        <h5 class="card-title">Trần Thị B</h5>
                        <p class="card-text text-muted">Quản lý Hỗ trợ Khách hàng</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4 fade-in">
                <div class="card h-100 text-center p-4">
                    <div class="card-body">
                        <img src="https://via.placeholder.com/150?text=Nhân viên+3" alt="Nhân viên 3" class="rounded-circle mb-3" style="width: 150px; height: 150px;">
                        <h5 class="card-title">Lê Văn C</h5>
                        <p class="card-text text-muted">Chuyên gia Chuỗi Cung ứng</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="py-5">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-12">
                <h2 class="fw-bold mb-3">Liên Hệ Với Chúng Tôi</h2>
                <p class="lead text-muted">Hãy liên hệ để nhận tư vấn và hỗ trợ từ đội ngũ của chúng tôi.</p>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card p-4 shadow-sm">
                    <div class="card-body text-center">
                        <p class="mb-3"><i class="fas fa-envelope"></i> Email: duyenb2203435@student.ctu.edu.vn</p>
                        <p class="mb-3"><i class="fas fa-phone"></i> Hotline: +84 123 456 789</p>
                        <p class="mb-0"><i class="fas fa-map-marker-alt"></i> Địa chỉ: 123 Đường ABC, TP. Cần Thơ</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
