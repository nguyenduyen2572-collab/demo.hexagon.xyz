<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'HEXAGON Admin')</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}?v=3">
    <link rel="stylesheet" href="{{ asset('css/hexagon-web-polish.css') }}?v=4">
    <script src="{{ asset('js/hexagon-web-polish.js') }}?v=4" defer></script>
</head>
<body>
<div class="admin-layout">
    <aside class="admin-sidebar">
        <div class="admin-brand">HEXAGON <small>ADMINISTRATION</small></div>
        <nav class="admin-nav" aria-label="Điều hướng quản trị">
            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
               href="{{ route('admin.dashboard') }}">▦ Tổng quan</a>
            <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}"
               href="{{ route('admin.orders.index') }}">▤ Quản lý đơn hàng</a>
               
            <a class="nav-link {{ request()->routeIs('admin.domain-prices.*') ? 'active' : '' }}"
                href="{{ route('admin.domain-prices.index') }}">
                ▤ Bảng giá tên miền
            </a>
            
            <a class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}"
            href="{{ route('admin.customers.index') }}">
                ♙ Quản lý khách hàng
            </a>


            <a class="nav-link" href="{{ url('/') }}" target="_blank" rel="noopener noreferrer">↗ Xem website</a>
        </nav>
        <p class="sidebar-note">HEXAGON · Môi trường thử nghiệm</p>
    </aside>

    <div class="admin-content">
        <header class="admin-topbar">
            <strong>@yield('topbar_title', 'Quản trị hệ thống')</strong>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button class="logout-button" type="submit">Đăng xuất</button>
            </form>
        </header>
        <main class="admin-main">
            @yield('content')
        </main>
    </div>
</div>
</body>
</html>
