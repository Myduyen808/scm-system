@extends('layouts.app')

@section('title', 'Quản Lý Người Dùng')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="mb-0">
                    <i class="fas fa-users text-primary"></i> Quản Lý Người Dùng
                </h1>
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Thêm Người Dùng
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.users') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <input type="text"
                               name="search"
                               class="form-control"
                               placeholder="Tìm kiếm tên hoặc email..."
                               value="{{ request('search') }}"
                               autocomplete="off">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-search"></i> Lọc
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-redo"></i> Làm mới
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Tên</th>
                            <th>Email</th>
                            <th>Vai trò</th>
                            <th width="200">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($roles->isNotEmpty())
                                    <select class="form-select form-select-sm user-role"
                                            data-user-id="{{ $user->id }}"
                                            data-current-role="{{ $user->roles->pluck('name')->first() ?? '' }}"
                                            style="width: 150px;">
                                        @foreach($roles as $role)
                                            <option value="{{ $role->name }}"
                                                    {{ $user->roles->pluck('name')->contains($role->name) ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    <span class="text-muted">Không có vai trò nào</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('admin.users.edit', $user->id) }}"
                                       class="btn btn-outline-primary"
                                       title="Chỉnh sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button"
                                            class="btn btn-outline-danger delete-user"
                                            data-user-id="{{ $user->id }}"
                                            data-user-name="{{ $user->name }}"
                                            title="Xóa">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-users fa-3x mb-3"></i>
                                    <p>Không có người dùng nào được tìm thấy.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($users->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $users->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Xác nhận xóa người dùng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Bạn có chắc muốn xóa người dùng <strong id="user-name"></strong>?
                    <br><small class="text-warning">Hành động này không thể hoàn tác!</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <form id="delete-form" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Xóa</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Update user role with confirmation
    $('.user-role').on('change', function() {
        confirmRoleChange(this);
    });

    function confirmRoleChange(select) {
        const userId = $(select).data('user-id');
        const newRole = $(select).val();
        const currentRole = $(select).data('current-role');

        if (newRole !== currentRole && !confirm(`Bạn có chắc muốn thay đổi vai trò sang ${newRole}?`)) {
            $(select).val(currentRole);
            return;
        }

        $.ajax({
            url: `/admin/users/${userId}/role`,
            method: 'PATCH',
            data: {
                _token: '{{ csrf_token() }}',
                role: newRole
            },
            success: function(response) {
                showToast('success', response.message);
                $(select).data('current-role', newRole);
            },
            error: function() {
                showToast('error', 'Có lỗi xảy ra khi cập nhật vai trò!');
                $(select).val(currentRole);
            }
        });
    }

    // Delete user
    $('.delete-user').on('click', function() {
        const userId = $(this).data('user-id');
        const userName = $(this).data('user-name');
        $('#user-name').text(userName);
        $('#delete-form').attr('action', `/admin/users/${userId}`);
        $('#deleteUserModal').modal('show');
    });

    // Toast notification function (Bootstrap 5)
    function showToast(type, message) {
        const toast = $(`
            <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-${type === 'success' ? 'check' : 'exclamation-triangle'}"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `);
        if (!$('.toast-container').length) {
            $('body').append('<div class="toast-container position-fixed top-0 end-0 p-3"></div>');
        }
        $('.toast-container').append(toast);
        const bsToast = new bootstrap.Toast(toast[0]);
        bsToast.show();
        setTimeout(() => toast.remove(), 5000);
    }
});
</script>
@endsection
