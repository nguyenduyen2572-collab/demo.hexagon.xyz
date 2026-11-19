@extends('admin.layouts.app')
@section('title', 'Quản lý nhân sự - HEXAGON Admin')
@section('topbar_title', 'Quản lý nhân sự')
@push('styles')<link rel="stylesheet" href="{{ asset('css/hexagon-admin-team-v9.css') }}?v=1">@endpush
@push('scripts')
<script defer>
document.addEventListener('DOMContentLoaded', function () {
  const select = document.getElementById('v9-role-select');
  if (!select) return;
  const sync = function () {
    document.querySelectorAll('[data-v9-permission]').forEach(function (checkbox) {
      const disable = select.value === 'staff' && checkbox.value !== 'saas';
      if (disable) checkbox.checked = false;
      checkbox.disabled = disable;
      checkbox.closest('label').style.opacity = disable ? '0.45' : '1';
    });
  };
  select.addEventListener('change', sync);
  sync();
});
</script>
@endpush
@section('content')
<div class="hx-team-v9">
    <div class="page-heading"><div><h1>Quản lý nhân sự</h1><p>Tạo tài khoản riêng, phân vai trò và cấp quyền theo từng mảng công việc.</p></div></div>
    @if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>@endif
    <section class="panel">
        <h2>Thêm tài khoản nhân sự</h2>
        <p class="hx-team-warning">Chỉ Super Admin được tạo tài khoản. Nhân viên thường chỉ được cấp quyền chỉnh nội dung SaaS trong V9.0; quyền nhận việc tư vấn và đơn hàng sẽ được mở sau khi hoàn thiện phạm vi dữ liệu được giao.</p>
        <form method="POST" action="{{ route('admin.team.store') }}" class="hx-team-form" autocomplete="off">
            @csrf
            <label>Họ tên <input name="name" value="{{ old('name') }}" required maxlength="150"></label>
            <label>Email đăng nhập <input type="email" name="email" value="{{ old('email') }}" required maxlength="190"></label>
            <label>Mật khẩu nhân viên <input type="password" name="password" required minlength="12" autocomplete="new-password"></label>
            <label>Nhập lại mật khẩu <input type="password" name="password_confirmation" required minlength="12" autocomplete="new-password"></label>
            <label>Vai trò <select name="role" id="v9-role-select" required><option value="staff" @selected(old('role')==='staff')>Nhân viên</option><option value="manager" @selected(old('role')==='manager')>Quản lý bộ phận</option></select></label>
            <div class="hx-team-span"><strong>Quyền đề xuất</strong><div class="hx-team-checks">
                @foreach($capabilities as $key => $name)<label><input type="checkbox" name="permissions[]" value="{{ $key }}" data-v9-permission="{{ $key }}" @checked(in_array($key, old('permissions', []), true))> {{ $name }}</label>@endforeach
            </div><p class="hx-team-hint">Phân quyền được kiểm tra phía máy chủ. Với nhân viên, V9.0 chỉ kích hoạt quyền Dịch vụ SaaS dù có gửi thêm quyền khác.</p></div>
            <div class="hx-team-span"><button class="hx-team-submit" type="submit">Tạo tài khoản</button></div>
        </form>
    </section>
    <section class="panel">
        <h2>Danh sách tài khoản quản trị</h2>
        <div class="table-wrap"><table class="admin-table"><thead><tr><th>Nhân sự</th><th>Vai trò</th><th>Quyền</th><th>Trạng thái</th><th>Thao tác</th></tr></thead><tbody>
        @forelse($users as $employee)<tr>
            <td><strong>{{ $employee->name }}</strong><br><small>{{ $employee->email }}</small></td>
            <td>{{ \App\Support\HexagonAdminRoles::displayRole($employee) }}</td>
            <td>{{ implode(', ', array_map(fn($p)=> $capabilities[$p] ?? $p, \App\Support\HexagonAdminRoles::capabilities($employee))) ?: 'Chưa cấp' }}</td>
            <td>{{ $employee->admin_enabled === false ? 'Tạm khóa' : 'Hoạt động' }}</td>
            <td>@if(\App\Support\HexagonAdminRoles::role($employee) !== 'super_admin')<a class="text-link" href="{{ route('admin.team.edit', $employee->id) }}">Cấp quyền</a>@else — @endif</td>
        </tr>@empty<tr><td colspan="5">Chưa có nhân sự.</td></tr>@endforelse
        </tbody></table></div>
        <div>{{ $users->links() }}</div>
    </section>
</div>
@endsection
