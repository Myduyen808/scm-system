@extends('layouts.app')

@section('title', 'Đăng ký')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header text-center bg-success text-white">
                    <h4><i class="fas fa-user-plus"></i> Đăng ký tài khoản</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">
                                <i class="fas fa-user"></i> Họ và tên
                            </label>
                            <input id="name" type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   name="name" value="{{ old('name') }}"
                                   required autocomplete="name" autofocus>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope"></i> Email
                            </label>
                            <input id="email" type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   name="email" value="{{ old('email') }}"
                                   required autocomplete="email">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock"></i> Mật khẩu
                            </label>
                            <input id="password" type="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   name="password" required autocomplete="new-password">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password-confirm" class="form-label">
                                <i class="fas fa-lock"></i> Xác nhận mật khẩu
                            </label>
                            <input id="password-confirm" type="password"
                                   class="form-control"
                                   name="password_confirmation" required autocomplete="new-password">
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-user-plus"></i> Đăng ký
                            </button>
                        </div>

                        <div class="text-center mt-3">
                            <span>Đã có tài khoản? </span>
                            <a href="{{ route('login') }}" class="text-decoration-none">
                                Đăng nhập ngay
                            </a>
                        </div>
                    </form>

                    <div class="mt-4 pt-3 border-top">
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i>
                            <strong>Lưu ý:</strong> Tài khoản mới sẽ được gán vai trò <strong>Customer</strong> mặc định.
                            Liên hệ admin để thay đổi vai trò khác.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
