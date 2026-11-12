@extends('customer.layouts.auth')

@section('title', 'Đăng ký | HEXAGON')
@section('auth_page', 'register')

@section('content')
<link rel="stylesheet" href="{{ asset('css/hexagon-register-ux.css') }}?v=1">

<div class="hx-reg" data-hexagon-registration>
    <h1 id="hxPageTitle" data-i18n="registerTitle">Tạo tài khoản</h1>

    @if(session('success'))
        <div class="hx-reg-success" role="status" aria-live="polite">
            <div class="hx-reg-success-icon" aria-hidden="true">✓</div>
            <h2 data-hx-reg-vi="Tạo tài khoản thành công!" data-hx-reg-en="Account created successfully!">Tạo tài khoản thành công!</h2>
            <p data-hx-reg-vi="Tài khoản của bạn đã được tạo. Vui lòng kiểm tra email để xác minh tài khoản trước khi sử dụng." data-hx-reg-en="Your account has been created. Please check your email and verify your account before using it.">Tài khoản của bạn đã được tạo. Vui lòng kiểm tra email để xác minh tài khoản trước khi sử dụng.</p>
            <a class="hx-reg-success-action" href="{{ route('customer.login') }}">
                <span data-hx-reg-vi="Tới trang đăng nhập ngay" data-hx-reg-en="Go to sign in now">Tới trang đăng nhập ngay</span>
                <span aria-hidden="true">→</span>
            </a>
        </div>
    @else
        <p class="hx-subtitle" data-i18n="registerLead">Bắt đầu hành trình tên miền của bạn với HEXAGON.</p>
        <form id="hxRegistrationForm" method="POST" action="{{ route('customer.register.store') }}" novalidate>
            @csrf
            <input type="hidden" name="ui_language" id="hxRegistrationLanguage" value="{{ old('ui_language', 'vi') }}">

            <div class="hx-group hx-reg-field">
                <label for="name" data-i18n="fullName">Họ và tên</label>
                <input class="hx-input" id="name" name="name" type="text" maxlength="150"
                       autocomplete="name" value="{{ old('name') }}" required
                       aria-describedby="hxRegNameError" aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}">
                <p class="hx-reg-error" id="hxRegNameError" aria-live="polite" data-server-error="{{ $errors->has('name') ? '1' : '0' }}">{{ $errors->first('name') }}</p>
            </div>

            <div class="hx-group hx-reg-field">
                <label for="email" data-i18n="email">Email</label>
                <input class="hx-input" id="email" name="email" type="email" inputmode="email" maxlength="190"
                       autocomplete="email" autocapitalize="none" spellcheck="false"
                       placeholder="ten@congty.com" value="{{ old('email') }}" required
                       aria-describedby="hxRegEmailError" aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}">
                <p class="hx-reg-error" id="hxRegEmailError" aria-live="polite" data-server-error="{{ $errors->has('email') ? '1' : '0' }}">{{ $errors->first('email') }}</p>
            </div>

            <div class="hx-group hx-reg-field">
                <label for="password" data-i18n="password">Mật khẩu</label>
                <div class="pw-field hx-reg-password-field">
                    <input class="hx-input" id="password" name="password" type="password"
                           minlength="8" autocomplete="new-password" required
                           aria-describedby="hxRegPasswordHint hxRegPasswordError" aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}">
                    <button class="pw-toggle" type="button" data-password-toggle data-target="password"
                            aria-controls="password" aria-pressed="false" aria-label="Hiển thị mật khẩu" title="Hiển thị mật khẩu">
                        <svg class="pw-icon pw-eye" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.2 12s3.5-6 9.8-6 9.8 6 9.8 6-3.5 6-9.8 6-9.8-6-9.8-6z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg class="pw-icon pw-eye-off" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 3l18 18M10.6 6.1A11.5 11.5 0 0 1 12 6c6.3 0 9.8 6 9.8 6a15 15 0 0 1-3.1 3.6M6.7 6.9C3.8 8.7 2.2 12 2.2 12s3.5 6 9.8 6c1.5 0 2.9-.3 4.1-.9"/><path d="M10 10a3 3 0 0 0 4 4"/></svg>
                    </button>
                </div>
                <p class="hx-reg-hint" id="hxRegPasswordHint" data-hx-reg-vi="Tối thiểu 8 ký tự: chữ hoa, chữ thường, số và ký tự đặc biệt." data-hx-reg-en="At least 8 characters: uppercase, lowercase, number and symbol.">Tối thiểu 8 ký tự: chữ hoa, chữ thường, số và ký tự đặc biệt.</p>
                <p class="hx-reg-error" id="hxRegPasswordError" aria-live="polite" data-server-error="{{ $errors->has('password') ? '1' : '0' }}">{{ $errors->first('password') }}</p>
            </div>

            <div class="hx-group hx-reg-field">
                <label for="password_confirmation" data-i18n="confirmPassword">Nhập lại mật khẩu</label>
                <div class="pw-field hx-reg-password-field">
                    <input class="hx-input" id="password_confirmation" name="password_confirmation" type="password"
                           minlength="8" autocomplete="new-password" required
                           aria-describedby="hxRegConfirmError" aria-invalid="{{ $errors->has('password_confirmation') ? 'true' : 'false' }}">
                    <button class="pw-toggle" type="button" data-password-toggle data-target="password_confirmation"
                            aria-controls="password_confirmation" aria-pressed="false" aria-label="Hiển thị mật khẩu" title="Hiển thị mật khẩu">
                        <svg class="pw-icon pw-eye" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.2 12s3.5-6 9.8-6 9.8 6 9.8 6-3.5 6-9.8 6-9.8-6-9.8-6z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg class="pw-icon pw-eye-off" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 3l18 18M10.6 6.1A11.5 11.5 0 0 1 12 6c6.3 0 9.8 6 9.8 6a15 15 0 0 1-3.1 3.6M6.7 6.9C3.8 8.7 2.2 12 2.2 12s3.5 6 9.8 6c1.5 0 2.9-.3 4.1-.9"/><path d="M10 10a3 3 0 0 0 4 4"/></svg>
                    </button>
                </div>
                <p class="hx-reg-error" id="hxRegConfirmError" aria-live="polite" data-server-error="{{ $errors->has('password_confirmation') ? '1' : '0' }}">{{ $errors->first('password_confirmation') }}</p>
            </div>

            <button class="hx-submit" type="submit">
                <span data-i18n="registerButton">Đăng ký tài khoản</span>
                <span class="hx-submit-arrow" aria-hidden="true">→</span>
            </button>
        </form>
        <div class="hx-divider" aria-hidden="true"><span>✦</span></div>
        <p class="hx-bottom"><span data-i18n="haveAccount">Đã có tài khoản?</span> <a href="{{ route('customer.login') }}" data-i18n="login">Đăng nhập</a></p>
    @endif
</div>
<script src="{{ asset('js/hexagon-register-ux.js') }}?v=2" defer></script>
@endsection
