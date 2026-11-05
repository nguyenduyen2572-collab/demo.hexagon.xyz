@extends('customer.layouts.auth')

@section('title', 'Xác minh email | HEXAGON')
@section('auth_page', 'verify')

@section('content')
    <h1 id="hxPageTitle" data-i18n="verifyTitle">Xác minh địa chỉ email</h1>
    <p class="hx-subtitle">
        <span data-i18n="verifyLead">Vui lòng xác minh email trước khi sử dụng khu vực tài khoản.</span>
        <strong class="hx-email">{{ $user->email }}</strong>
    </p>

    @if(session('status'))
        <div class="hx-alert hx-alert-success" role="status">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button class="hx-submit" type="submit">
            <span data-i18n="verifyButton">Gửi lại liên kết xác minh</span>
            <span class="hx-submit-arrow" aria-hidden="true">→</span>
        </button>
    </form>


    <div class="hx-local-note" data-i18n="localVerifyNote">
    </div>
@endsection
