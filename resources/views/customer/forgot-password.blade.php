@extends('customer.layouts.auth')

@section('title', 'Quên mật khẩu | HEXAGON')
@section('auth_page', 'forgot')

@section('content')
    <h1 id="hxPageTitle" data-i18n="forgotTitle">Quên mật khẩu?</h1>
    <p class="hx-subtitle" data-i18n="forgotLead">Nhập email để nhận liên kết đặt lại mật khẩu.</p>

    @if(session('status'))
        <div class="hx-alert hx-alert-success" role="status">{{ session('status') }}</div>
    @endif

    <form id="hxForgotForm" method="POST" action="{{ route('password.email') }}" novalidate>
        @csrf
        <input type="hidden" name="hx_locale" id="hxForgotLocale" value="vi">
        <div class="hx-group">
            <label for="email" data-i18n="email">Email</label>
            <input class="hx-input" type="email" id="email" name="email"
                   maxlength="190" autocomplete="email" value="{{ old('email') }}" required aria-describedby="hxForgotEmailError" aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}">
            <p class="hx-forgot-error" id="hxForgotEmailError" role="alert">@error('email'){{ $message }}@enderror</p>
        </div>
        <button class="hx-submit hx-forgot-submit" type="submit">
            <span data-i18n="forgotButton">Gửi liên kết đặt lại mật khẩu</span>
        </button>
    </form>
@endsection

@push('hxForgotStyles')
<link rel="stylesheet" href="{{ asset('css/hexagon-forgot-password-ux.css') }}?v=1">
@endpush
@push('hxForgotScripts')
<script src="{{ asset('js/hexagon-forgot-password-ux.js') }}?v=1" defer></script>
@endpush
