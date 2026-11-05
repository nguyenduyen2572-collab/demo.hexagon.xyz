@extends('customer.layouts.auth')

@section('title', 'Quên mật khẩu | HEXAGON')
@section('auth_page', 'forgot')

@section('content')
    <h1 id="hxPageTitle" data-i18n="forgotTitle">Quên mật khẩu?</h1>
    <p class="hx-subtitle" data-i18n="forgotLead">Nhập email để nhận liên kết đặt lại mật khẩu.</p>

    @if(session('status'))
        <div class="hx-alert hx-alert-success" role="status">{{ session('status') }}</div>
    @endif
    @if($errors->any())
        <div class="hx-alert hx-alert-error" role="alert">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="hx-group">
            <label for="email" data-i18n="email">Email</label>
            <input class="hx-input" type="email" id="email" name="email"
                   maxlength="190" autocomplete="email" value="{{ old('email') }}" required>
        </div>
        <button class="hx-submit" type="submit">
            <span data-i18n="forgotButton">Gửi liên kết đặt lại mật khẩu</span>
            <span class="hx-submit-arrow" aria-hidden="true">→</span>
        </button>
    </form>


    <div class="hx-local-note" data-i18n="localEmailNote">
        Chế độ Local: liên kết thử nghiệm nằm trong storage/logs/laravel.log.
    </div>
@endsection
