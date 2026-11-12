<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Tài khoản khách hàng - HEXAGON')</title>
    <link rel="stylesheet" href="{{ asset('css/customer-area.css') }}?v=1">
    <link rel="stylesheet" href="{{ asset('css/customer-settings.css') }}?v=1">
    <link rel="stylesheet" href="{{ asset('css/hexagon-password-toggle.css') }}?v=1">
    <script src="{{ asset('js/hexagon-password-toggle.js') }}?v=1" defer></script>
    <link rel="stylesheet" href="{{ asset('css/hexagon-auth-cursor-state.css') }}?v=1">
    <script src="{{ asset('js/hexagon-auth-login-state.js') }}?v=1" defer></script>
    <link rel="stylesheet" href="{{ asset('css/hexagon-web-polish.css') }}?v=4">
    <script src="{{ asset('js/hexagon-web-polish.js') }}?v=4" defer></script>
    <link rel="stylesheet" href="{{ asset('css/hexagon-unified-ui-v1.css') }}?v=1">
    <script src="{{ asset('js/hexagon-unified-ui-v1.js') }}?v=1" defer></script>
    <!-- HEXAGON_FONT_SOURCE_SANS_V1_START -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@400;500;600;700;800&amp;display=swap">
    <link rel="stylesheet" href="{{ asset('css/hexagon-font-source-sans-v1.css') }}?v=1">
    <!-- HEXAGON_FONT_SOURCE_SANS_V1_END -->
</head>
<body>
<header class="ca-header">
    <a href="{{ url('/') }}" class="ca-logo">HEXAGON</a>
    
</header>
<main class="ca-main">
    <div class="cs-heading">
        <div>
            <h1 class="ca-title">@yield('heading')</h1>
            <p class="ca-subtitle">@yield('description')</p>
        </div>
        
    </div>
    <nav class="cs-tabs" aria-label="Cài đặt tài khoản">
        <a class="{{ request()->routeIs('customer.account.profile.*') ? 'current' : '' }}"
           href="{{ route('customer.account.profile.edit') }}">Thông tin cá nhân</a>
        <a class="{{ request()->routeIs('customer.account.password.*') ? 'current' : '' }}"
           href="{{ route('customer.account.password.edit') }}">Đổi mật khẩu</a>
        <a href="{{ route('customer.account.orders.index') }}">Đơn hàng của tôi</a>
    </nav>
    @if(session('success'))
        <div class="cs-alert cs-success" role="status">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="cs-alert cs-error" role="alert">
            <strong>Vui lòng kiểm tra thông tin:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @yield('content')
</main>
</body>
</html>
