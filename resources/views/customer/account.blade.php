@extends('customer.layouts.account-shell')

@section('title', 'Tài khoản của tôi - HEXAGON')

@section('content')
{{-- hx-security-account-v2 --}}
<link rel="stylesheet" href="{{ asset('css/hexagon-password-security-v2.css') }}?v=1">
@php
    $passwordHasErrors = $errors->has('current_password') || $errors->has('new_password') || $errors->has('new_password_confirmation');
    $initialTab = $passwordHasErrors ? 'security' : (session('account_tab') === 'security' || request('tab') === 'security' || request('tab') === 'password' ? 'security' : 'profile');
@endphp
<div class="hx-page-heading">
    <div>
        <p class="hx-eyebrow" data-vi="TRUNG TÂM TÀI KHOẢN" data-en="ACCOUNT CENTER">TRUNG TÂM TÀI KHOẢN</p>
        <h1 data-vi="Tài khoản của tôi" data-en="My account">Tài khoản của tôi</h1>
        <p data-vi="Quản lý hồ sơ và bảo mật ở một nơi duy nhất." data-en="Manage your profile and security in one place.">Quản lý hồ sơ và bảo mật ở một nơi duy nhất.</p>
    </div>
</div>

@if(session('success'))
    <div class="hx-alert hx-alert-success" role="status">{{ session('success') }}</div>
@endif

<section class="hx-card hx-settings-card" data-hx-settings data-initial-tab="{{ $initialTab }}">
    <div class="hx-tabbar" role="tablist" aria-label="Cài đặt tài khoản">
        <button type="button" class="hx-tab" role="tab" id="hx-tab-profile" data-hx-tab="profile"
                aria-selected="{{ $initialTab === 'profile' ? 'true' : 'false' }}" aria-controls="hx-panel-profile"
                tabindex="{{ $initialTab === 'profile' ? '0' : '-1' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4.5 21c0-4.2 3.2-7 7.5-7s7.5 2.8 7.5 7"/></svg>
            <span data-vi="Thông tin cá nhân" data-en="Personal information">Thông tin cá nhân</span>
        </button>
        <button type="button" class="hx-tab" role="tab" id="hx-tab-security" data-hx-tab="security"
                aria-selected="{{ $initialTab === 'security' ? 'true' : 'false' }}" aria-controls="hx-panel-security"
                tabindex="{{ $initialTab === 'security' ? '0' : '-1' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="5" y="10" width="14" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
            <span data-vi="Bảo mật & mật khẩu" data-en="Security & password">Bảo mật & mật khẩu</span>
        </button>
    </div>

    <section id="hx-panel-profile" class="hx-tab-panel" role="tabpanel" tabindex="0" aria-labelledby="hx-tab-profile" data-hx-panel="profile"
             {{ $initialTab === 'profile' ? '' : 'hidden' }}>
        <div class="hx-section-heading">
            <h2 data-vi="Thông tin cá nhân" data-en="Personal information">Thông tin cá nhân</h2>
            <p data-vi="Cập nhật họ tên hiển thị trên tài khoản HEXAGON." data-en="Update the name displayed on your HEXAGON account.">Cập nhật họ tên hiển thị trên tài khoản HEXAGON.</p>
        </div>
        <form method="POST" action="{{ route('customer.account.profile.update') }}" class="hx-form">
            @csrf
            @method('PATCH')
            <label for="hx-profile-name" data-vi="Họ và tên" data-en="Full name">Họ và tên</label>
            <input id="hx-profile-name" type="text" name="name" value="{{ old('name', $user->name) }}"
                   maxlength="150" autocomplete="name" required>
            @error('name')<div class="hx-field-error" role="alert">{{ $message }}</div>@enderror

            <label for="hx-profile-email" data-vi="Email tài khoản" data-en="Account email">Email tài khoản</label>
            <input id="hx-profile-email" type="email" value="{{ $user->email }}" readonly aria-readonly="true">
            <p class="hx-hint" data-vi="Email đã liên kết với tài khoản. Hiện chưa hỗ trợ thay đổi tại đây." data-en="This email is linked to your account and cannot be changed here yet.">Email đã liên kết với tài khoản. Hiện chưa hỗ trợ thay đổi tại đây.</p>

            <div class="hx-form-actions">
                <button type="submit" class="hx-primary-button" data-vi="Lưu thay đổi" data-en="Save changes">Lưu thay đổi</button>
            </div>
        </form>
    </section>

    <section id="hx-panel-security" class="hx-tab-panel" role="tabpanel" tabindex="0" aria-labelledby="hx-tab-security" data-hx-panel="security"
             {{ $initialTab === 'security' ? '' : 'hidden' }}>
        <div class="hx-section-heading">
            <h2 data-vi="Đổi mật khẩu" data-en="Change password">Đổi mật khẩu</h2>
            <p data-vi="Nhập mật khẩu hiện tại để bảo vệ tài khoản của bạn." data-en="Enter your current password to protect your account.">Nhập mật khẩu hiện tại để bảo vệ tài khoản của bạn.</p>
        </div>
        <form method="POST" action="{{ route('customer.account.password.update') }}" class="hx-form" autocomplete="on" novalidate>
            @csrf
            @method('PATCH')
            <input type="hidden" name="ui_language" data-hx-password-locale value="vi">
            <label for="hx-current-password" data-vi="Mật khẩu hiện tại" data-en="Current password">Mật khẩu hiện tại</label>
            <div class="hx-password-field">
                <input id="hx-current-password" name="current_password" type="password" autocomplete="current-password" required>
                <button type="button" data-hx-eye="hx-current-password" aria-label="Hiển thị mật khẩu" aria-pressed="false" title="Hiển thị mật khẩu">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.7-6 10-6 10 6 10 6-3.7 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/><path class="hx-eye-slash" d="M3 3 21 21"/></svg>
                </button>
            </div>
            @error('current_password')<div class="hx-field-error" role="alert">{{ $message }}</div>@enderror

            <label for="hx-new-password" data-vi="Mật khẩu mới" data-en="New password">Mật khẩu mới</label>
            <div class="hx-password-field">
                <input id="hx-new-password" name="new_password" type="password" autocomplete="new-password" minlength="8" required>
                <button type="button" data-hx-eye="hx-new-password" aria-label="Hiển thị mật khẩu" aria-pressed="false" title="Hiển thị mật khẩu">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.7-6 10-6 10 6 10 6-3.7 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/><path class="hx-eye-slash" d="M3 3 21 21"/></svg>
                </button>
            </div>
            <p class="hx-hint" data-vi="Tối thiểu 8 ký tự, gồm chữ hoa, chữ thường, số và ký tự đặc biệt; khác mật khẩu cũ." data-en="At least 8 characters, including upper/lowercase, a number and a symbol; different from your old password.">Tối thiểu 8 ký tự, gồm chữ hoa, chữ thường, số và ký tự đặc biệt; khác mật khẩu cũ.</p>
            @error('new_password')<div class="hx-field-error" role="alert">{{ $message }}</div>@enderror

            <label for="hx-confirm-password" data-vi="Xác nhận mật khẩu mới" data-en="Confirm new password">Xác nhận mật khẩu mới</label>
            <div class="hx-password-field">
                <input id="hx-confirm-password" name="new_password_confirmation" type="password" autocomplete="new-password" minlength="8" required>
                <button type="button" data-hx-eye="hx-confirm-password" aria-label="Hiển thị mật khẩu" aria-pressed="false" title="Hiển thị mật khẩu">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.7-6 10-6 10 6 10 6-3.7 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/><path class="hx-eye-slash" d="M3 3 21 21"/></svg>
                </button>
            </div>
            @error('new_password_confirmation')<p class="hx-security-inline-error" role="alert">{{ $message }}</p>@enderror
            <div class="hx-form-actions">
                <button type="submit" class="hx-primary-button" data-vi="Cập nhật mật khẩu" data-en="Update password">Cập nhật mật khẩu</button>
            </div>
        </form>
        <p class="hx-safety-note" data-vi="Website hiện chạy trên Local HTTP. Chỉ sử dụng mật khẩu thử nghiệm, không dùng lại mật khẩu thật." data-en="This website currently runs on local HTTP. Use test passwords only, not real ones.">Website hiện chạy trên Local HTTP. Chỉ sử dụng mật khẩu thử nghiệm, không dùng lại mật khẩu thật.</p>
    </section>
</section>
<script src="{{ asset('js/hexagon-password-security-v2.js') }}?v=1" defer></script>
@endsection
