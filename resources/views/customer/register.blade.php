@extends('customer.layouts.auth')

@section('title', 'Đăng ký | HEXAGON')
@section('auth_page', 'register')

@section('content')
    <h1 id="hxPageTitle" data-i18n="registerTitle">Tạo tài khoản</h1>
    <p class="hx-subtitle" data-i18n="registerLead">Bắt đầu hành trình tên miền của bạn với HEXAGON.</p>

    @if(session('success'))
        <div class="hx-alert hx-alert-success" role="status">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="hx-alert hx-alert-error" role="alert">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('customer.register.store') }}">
        @csrf
        <div class="hx-group">
            <label for="name" data-i18n="fullName">Họ và tên</label>
            <input class="hx-input" id="name" name="name" type="text" maxlength="150"
                   autocomplete="name" value="{{ old('name') }}" required>
        </div>
        <div class="hx-group">
            <label for="email" data-i18n="email">Email</label>
            <input class="hx-input" id="email" name="email" type="email" maxlength="190"
                   autocomplete="email" value="{{ old('email') }}" required>
        </div>
        <div class="hx-group">
            <label for="password" data-i18n="password">Mật khẩu</label>
        <div class="pw-field">
            <input class="hx-input" id="password" name="password" type="password"
                   minlength="12" autocomplete="new-password" required>
            <button class="pw-toggle" type="button" data-password-toggle data-target="password"
                    aria-controls="password" aria-pressed="false" aria-label="Hiển thị mật khẩu" title="Hiển thị mật khẩu">
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
            <small class="hx-hint" data-i18n="passwordHint">Ít nhất 12 ký tự.</small>
        </div>
        <div class="hx-group">
            <label for="password_confirmation" data-i18n="confirmPassword">Nhập lại mật khẩu</label>
        <div class="pw-field">
            <input class="hx-input" id="password_confirmation" name="password_confirmation"
                   type="password" minlength="12" autocomplete="new-password" required>
            <button class="pw-toggle" type="button" data-password-toggle data-target="password_confirmation"
                    aria-controls="password_confirmation" aria-pressed="false" aria-label="Hiển thị mật khẩu" title="Hiển thị mật khẩu">
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
        </div>
        <button class="hx-submit" type="submit">
            <span data-i18n="registerButton">Đăng ký tài khoản</span>
            <span class="hx-submit-arrow" aria-hidden="true">→</span>
        </button>
    </form>
    <div class="hx-divider" aria-hidden="true"><span>✦</span></div>
    <p class="hx-bottom">
        <span data-i18n="haveAccount">Đã có tài khoản?</span>
        <a href="{{ route('customer.login') }}" data-i18n="login">Đăng nhập</a>
    </p>
@endsection
