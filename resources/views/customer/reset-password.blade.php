@extends('customer.layouts.auth')

@section('title', 'Đặt lại mật khẩu | HEXAGON')
@section('auth_page', 'reset')

@section('content')
{{-- hx-security-reset-v2 --}}
{{-- HEXAGON_RESET_INLINE_ERRORS_V2_1 --}}
<link rel="stylesheet" href="{{ asset('css/hexagon-password-security-v2.css') }}?v=1">
    <h1 id="hxPageTitle" data-i18n="resetTitle">Đặt lại mật khẩu</h1>
    <p class="hx-subtitle" data-i18n="resetLead">Tạo mật khẩu mới tối thiểu 8 ký tự, có chữ hoa, chữ thường, số và ký tự đặc biệt.</p>

<form method="POST" action="{{ route('password.update') }}" novalidate>
        @csrf
        <input type="hidden" name="ui_language" data-hx-password-locale value="vi">
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="hx-group">
            <label for="email" data-i18n="email">Email</label>
            <input class="hx-input" type="email" id="email" name="email" maxlength="190"
                   autocomplete="email" value="{{ old('email', $email) }}" required>
            <p class="hx-security-inline-error" role="alert" aria-live="polite" {{ $errors->has('email') ? '' : 'hidden' }}>{{ $errors->first('email') }}</p>
            <p class="hx-security-inline-error" role="alert" aria-live="polite" {{ $errors->has('token') ? '' : 'hidden' }}>{{ $errors->first('token') }}</p>
        </div>
        <div class="hx-group">
            <label for="password" data-i18n="newPassword">Mật khẩu mới</label>
        <div class="pw-field">
            <input class="hx-input" type="password" id="password" name="password"
                   minlength="8" autocomplete="new-password" required>
            
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
            <p class="hx-security-inline-error" role="alert" aria-live="polite" {{ $errors->has('password') ? '' : 'hidden' }}>{{ $errors->first('password') }}</p>
        </div>
        <div class="hx-group">
            <label for="password_confirmation" data-i18n="confirmNewPassword">Nhập lại mật khẩu mới</label>
        <div class="pw-field">
            <input class="hx-input" type="password" id="password_confirmation"
                   name="password_confirmation" minlength="8" autocomplete="new-password" required>
            
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
            <p class="hx-security-inline-error" role="alert" aria-live="polite" {{ $errors->has('password_confirmation') ? '' : 'hidden' }}>{{ $errors->first('password_confirmation') }}</p>
        </div>
        <button class="hx-submit" type="submit">
            <span data-i18n="resetButton">Lưu mật khẩu mới</span>
            <span class="hx-submit-arrow" aria-hidden="true">→</span>
        </button>
    </form>


<script src="{{ asset('js/hexagon-password-security-v2.js') }}?v=1" defer></script>
@endsection
