@extends('layouts.app')

@section('title', 'Đăng nhập')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header text-center bg-primary text-white">
                    <h4><i class="fas fa-sign-in-alt"></i> Đăng nhập</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope"></i> Email
                            </label>
                            <input id="email" type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   name="email" value="{{ old('email') }}"
                                   required autocomplete="email" autofocus>
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
                                   name="password" required autocomplete="current-password">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                       name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    Ghi nhớ đăng nhập
                                </label>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt"></i> Đăng nhập
                            </button>
                        </div>

                        <div class="text-center mt-3">
                            @if (Route::has('password.request'))
                                <a class="btn btn-link" href="{{ route('password.request') }}">
                                    Quên mật khẩu?
                                </a>
                            @endif

                            @if (Route::has('register'))
                                <div class="mt-2">
                                    <span>Chưa có tài khoản? </span>
                                    <a href="{{ route('register') }}" class="text-decoration-none">
                                        Đăng ký ngay
                                    </a>
                                </div>
                            @endif
                        </div>
                    </form>

                    {{-- <!-- Demo Accounts -->
                    <div class="mt-4 pt-3 border-top">
                        <h6 class="text-muted text-center">Tài khoản demo:</h6>
                        <div class="row">
                            <div class="col-6">
                                <small class="d-block"><strong>Admin:</strong></small>
                                <small>admin@example.com</small>
                            </div>
                            <div class="col-6">
                                <small class="d-block"><strong>Customer:</strong></small>
                                <small>customer@example.com</small>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-6">
                                <small class="d-block"><strong>Employee:</strong></small>
                                <small>employee@example.com</small>
                            </div>
                            <div class="col-6">
                                <small class="d-block"><strong>Supplier:</strong></small>
                                <small>supplier@example.com</small>
                            </div>
                        </div>
                        <small class="d-block text-center mt-2 text-muted">
                            Mật khẩu: <code>password</code>
                        </small>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
