@extends('customer.layouts.settings')

@section('title', 'Đổi mật khẩu - HEXAGON')
@section('heading', 'Đổi mật khẩu')
@section('description', 'Bảo vệ tài khoản bằng mật khẩu mới và không chia sẻ mật khẩu với người khác.')

@section('content')
<section class="ca-card cs-card">
    <h2 class="ca-card-title">Bảo mật tài khoản</h2>
    <p class="ca-note">Nhập mật khẩu đang sử dụng, sau đó thiết lập mật khẩu mới có ít nhất 12 ký tự.</p>

    <form method="POST" action="{{ route('customer.account.password.update') }}">
        @csrf
        @method('PATCH')

        <label class="cs-label" for="current_password">Mật khẩu hiện tại</label>
        <div class="pw-field">
            <input class="cs-input" id="current_password" type="password"
               name="current_password" autocomplete="current-password" required>
            <button class="pw-toggle" type="button" data-password-toggle data-target="current_password"
                    aria-controls="current_password" aria-pressed="false" aria-label="Hiển thị mật khẩu" title="Hiển thị mật khẩu">
                <svg class="pw-icon pw-eye" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M2.2 12s3.5-6 9.8-6 9.8 6 9.8 6-3.5 6-9.8 6-9.8-6-9.8-6z" />
                    <circle cx="12" cy="12" r="3" />
                </svg>
                <svg class="pw-icon pw-eye-off" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M3 3l18 18M10.6 6.1A11.5 11.5 0 0 1 12 6c6.3 0 9.8 6 9.8 6a15 15 0 0 1-3.1 3.6M6.7 6.9C3.8 8.7 2.2 12 2.2 12s3.5 6 9.8 6c1.5 0 2.9-.3 4.1-.9" />
                    <path d="M10 10a3 3 0 0 0 4 4" />
                </svg>
            </button>
        </div>
        @error('current_password')<p class="cs-field-error">{{ $message }}</p>@enderror

        <label class="cs-label" for="new_password">Mật khẩu mới</label>
        <div class="pw-field">
            <input class="cs-input" id="new_password" type="password"
               name="new_password" autocomplete="new-password" minlength="12" required>
            <button class="pw-toggle" type="button" data-password-toggle data-target="new_password"
                    aria-controls="new_password" aria-pressed="false" aria-label="Hiển thị mật khẩu" title="Hiển thị mật khẩu">
                <svg class="pw-icon pw-eye" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M2.2 12s3.5-6 9.8-6 9.8 6 9.8 6-3.5 6-9.8 6-9.8-6-9.8-6z" />
                    <circle cx="12" cy="12" r="3" />
                </svg>
                <svg class="pw-icon pw-eye-off" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M3 3l18 18M10.6 6.1A11.5 11.5 0 0 1 12 6c6.3 0 9.8 6 9.8 6a15 15 0 0 1-3.1 3.6M6.7 6.9C3.8 8.7 2.2 12 2.2 12s3.5 6 9.8 6c1.5 0 2.9-.3 4.1-.9" />
                    <path d="M10 10a3 3 0 0 0 4 4" />
                </svg>
            </button>
        </div>
        <p class="cs-help">Ít nhất 12 ký tự và khác với mật khẩu hiện tại.</p>
        @error('new_password')<p class="cs-field-error">{{ $message }}</p>@enderror

        <label class="cs-label" for="new_password_confirmation">Nhập lại mật khẩu mới</label>
        <div class="pw-field">
            <input class="cs-input" id="new_password_confirmation" type="password"
               name="new_password_confirmation" autocomplete="new-password" minlength="12" required>
            <button class="pw-toggle" type="button" data-password-toggle data-target="new_password_confirmation"
                    aria-controls="new_password_confirmation" aria-pressed="false" aria-label="Hiển thị mật khẩu" title="Hiển thị mật khẩu">
                <svg class="pw-icon pw-eye" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M2.2 12s3.5-6 9.8-6 9.8 6 9.8 6-3.5 6-9.8 6-9.8-6-9.8-6z" />
                    <circle cx="12" cy="12" r="3" />
                </svg>
                <svg class="pw-icon pw-eye-off" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M3 3l18 18M10.6 6.1A11.5 11.5 0 0 1 12 6c6.3 0 9.8 6 9.8 6a15 15 0 0 1-3.1 3.6M6.7 6.9C3.8 8.7 2.2 12 2.2 12s3.5 6 9.8 6c1.5 0 2.9-.3 4.1-.9" />
                    <path d="M10 10a3 3 0 0 0 4 4" />
                </svg>
            </button>
        </div>

        <div class="cs-actions">
            <button class="cs-primary" type="submit">Cập nhật mật khẩu</button>
            <a class="cs-secondary" href="{{ route('customer.account.index') }}">Hủy</a>
        </div>
    </form>
    <div class="cs-security-tip">Đây là môi trường Local đang chạy HTTP. Hãy sử dụng mật khẩu thử nghiệm, không dùng lại mật khẩu thật.</div>
</section>
@endsection
