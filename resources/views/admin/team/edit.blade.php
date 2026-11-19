@extends('admin.layouts.app')
@section('title', 'Cấp quyền nhân sự - HEXAGON Admin')
@section('topbar_title', 'Cấp quyền nhân sự')
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
    <div class="page-heading"><div><h1>Cấp quyền nhân sự</h1><p>{{ $employee->name }} — {{ $employee->email }}</p></div><a class="text-link" href="{{ route('admin.team.index') }}">← Quay lại danh sách</a></div>
    <section class="panel">
        @if($errors->any())<div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>@endif
        <form method="POST" action="{{ route('admin.team.update', $employee->id) }}" class="hx-team-form">
            @csrf @method('PATCH')
            <label>Họ tên <input name="name" value="{{ old('name',$employee->name) }}" required maxlength="150"></label>
            <label>Vai trò <select name="role" id="v9-role-select"><option value="staff" @selected(old('role',$employee->admin_role)==='staff')>Nhân viên</option><option value="manager" @selected(old('role',$employee->admin_role)==='manager')>Quản lý bộ phận</option></select></label>
            <label>Trạng thái <select name="admin_enabled"><option value="1" @selected((string) old('admin_enabled',(int)$employee->admin_enabled)==='1')>Hoạt động</option><option value="0" @selected((string) old('admin_enabled',(int)$employee->admin_enabled)==='0')>Tạm khóa</option></select></label>
            <div class="hx-team-span"><strong>Quyền theo mảng</strong><div class="hx-team-checks">
                @foreach($capabilities as $key=>$name)<label><input type="checkbox" name="permissions[]" value="{{ $key }}" data-v9-permission="{{ $key }}" @checked(in_array($key, old('permissions',$granted), true))> {{ $name }}</label>@endforeach
            </div><p class="hx-team-hint">Quản lý có thể được cấp nhiều mảng. Nhân viên chỉ được dùng Dịch vụ SaaS ở V9.0.</p></div>
            <div class="hx-team-span"><button class="hx-team-submit" type="submit">Lưu quyền nhân sự</button></div>
        </form>
    </section>
</div>
@endsection
