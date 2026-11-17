@extends('admin.layouts.app')

@section('title', 'Đổi mật khẩu quản trị - HEXAGON')
@section('topbar_title', 'Bảo mật tài khoản')

@section('content')
<section class="hx-security-v4">
    <div class="hx-security-v4-heading">
        <div>
            <p class="hx-security-v4-eyebrow">TÀI KHOẢN QUẢN TRỊ</p>
            <h1>Đổi mật khẩu</h1>
            <p>Bảo vệ quyền truy cập Admin bằng mật khẩu mạnh. Sau khi lưu, bạn sẽ cần đăng nhập lại.</p>
        </div>
    </div>

    <div class="hx-security-v4-card">
        <div class="hx-security-v4-account"><span>Đang đăng nhập</span><strong>{{ auth('admin')->user()?->email }}</strong></div>
        <form method="POST" action="{{ route('admin.security.password.update') }}" class="hx-security-v4-form" autocomplete="off">
            @csrf
            @method('PATCH')

            <div class="hx-security-v4-field">
                <label for="current_password">Mật khẩu hiện tại <span aria-hidden="true">*</span></label>
                <input type="password" id="current_password" name="current_password" required autocomplete="current-password" aria-invalid="{{ $errors->has('current_password') ? 'true' : 'false' }}" aria-describedby="{{ $errors->has('current_password') ? 'current_password_error' : '' }}">
                @error('current_password') <p class="hx-security-v4-error" id="current_password_error" role="alert">{{ $message }}</p> @enderror
            </div>

            <div class="hx-security-v4-field">
                <label for="new_password">Mật khẩu mới <span aria-hidden="true">*</span></label>
                <input type="password" id="new_password" name="new_password" required minlength="12" maxlength="128" autocomplete="new-password" aria-invalid="{{ $errors->has('new_password') ? 'true' : 'false' }}" aria-describedby="new_password_hint{{ $errors->has('new_password') ? ' new_password_error' : '' }}">
                <p class="hx-security-v4-hint" id="new_password_hint">Ít nhất 12 ký tự, gồm chữ hoa, chữ thường, số và ký tự đặc biệt.</p>
                @error('new_password') <p class="hx-security-v4-error" id="new_password_error" role="alert">{{ $message }}</p> @enderror
            </div>

            <div class="hx-security-v4-field">
                <label for="new_password_confirmation">Nhập lại mật khẩu mới <span aria-hidden="true">*</span></label>
                <input type="password" id="new_password_confirmation" name="new_password_confirmation" required minlength="12" maxlength="128" autocomplete="new-password">
            </div>

            <div class="hx-security-v4-notice">Vì lý do bảo mật, phiên Admin hiện tại sẽ kết thúc sau khi đổi mật khẩu thành công.</div>
            <div class="hx-security-v4-actions">
                <button type="submit" class="hx-security-v4-submit">Lưu mật khẩu mới</button>
                <a href="{{ route('admin.dashboard') }}">Hủy</a>
            </div>
        </form>
    </div>
</section>
@endsection
