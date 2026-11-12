<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#101e38">
    <title>Đăng nhập quản trị - HEXAGON</title>
    <link rel="stylesheet" href="{{ asset('css/hexagon-admin-pro-v1.css') }}?v=1">
    <script src="{{ asset('js/hexagon-admin-pro-v1.js') }}?v=1" defer></script>
    <!-- HEXAGON_FONT_SOURCE_SANS_V1_START -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@400;500;600;700;800&amp;display=swap">
    <link rel="stylesheet" href="{{ asset('css/hexagon-font-source-sans-v1.css') }}?v=1">
    <!-- HEXAGON_FONT_SOURCE_SANS_V1_END -->
</head>
<body class="hx-admin-login">
    <main class="hx-login-card">
        <div class="hx-login-brand"><span class="hx-login-brand-mark">H</span><span>HEXAGON<b>.</b></span></div>
        <h1>Đăng nhập quản trị</h1>
        <p class="hx-login-subtitle">Truy cập trung tâm điều hành HEXAGON.</p>
        @if(session('status'))
            <div class="hx-login-error" role="status">{{ session('status') }}</div>
        @endif
        @if($errors->any())
            <div class="hx-login-error" role="alert">{{ $errors->first() }}</div>
        @endif
        <form method="POST" action="{{ route('admin.login.submit') }}">
            @csrf
            <div class="hx-login-field">
                <label for="hx-admin-email">Địa chỉ email</label>
                <input id="hx-admin-email" type="email" name="email" value="{{ old('email') }}" autocomplete="username" placeholder="Email quản trị" required autofocus>
            </div>
            <div class="hx-login-field">
                <label for="hx-admin-password">Mật khẩu</label>
                <div class="hx-login-password">
                    <input id="hx-admin-password" type="password" name="password" autocomplete="current-password" placeholder="Nhập mật khẩu" required>
                    <button type="button" data-password-toggle aria-controls="hx-admin-password" aria-pressed="false">Hiện</button>
                </div>
            </div>
            <button class="hx-login-submit" type="submit">Đăng nhập</button>
        </form>
        <div class="hx-login-bottom"><a href="{{ url('/') }}">← Quay lại trang chủ</a></div>
    </main>
</body>
</html>
