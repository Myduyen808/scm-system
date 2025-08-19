@extends('layouts.app')

@section('title', 'Cài Đặt Hệ Thống')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="mb-0">
                    <i class="fas fa-cog text-primary"></i> Cài Đặt Hệ Thống
                </h1>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Thông Tin Hệ Thống</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.update') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="site_name" class="form-label">Tên trang web</label>
                            <input type="text" class="form-control @error('site_name') is-invalid @endif" id="site_name" name="site_name" value="{{ old('site_name', $settings->site_name) }}" required>
                            @error('site_name') <div class="invalid-feedback">{{ $message }}</div> @endif
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email liên hệ</label>
                            <input type="email" class="form-control @error('email') is-invalid @endif" id="email" name="email" value="{{ old('email', $settings->email) }}" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @endif
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control @error('phone') is-invalid @endif" id="phone" name="phone" value="{{ old('phone', $settings->phone) }}" required>
                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @endif
                        </div>
                        <button type="submit" class="btn btn-primary">Lưu cài đặt</button>
                    </form>
                </div>
            </div>

            <!-- Form sao lưu database -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Sao lưu Database</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.backup') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-database"></i> Sao lưu database
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
