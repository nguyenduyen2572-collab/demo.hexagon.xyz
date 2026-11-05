<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Tài khoản HEXAGON')</title>
    <link rel="stylesheet" href="{{ asset('css/hexagon-account-unified.css') }}?v=1">
    <script src="{{ asset('js/hexagon-account-unified.js') }}?v=1" defer></script>
    <link rel="stylesheet" href="{{ asset('css/hexagon-auth-cursor-state.css') }}?v=1">
    <script src="{{ asset('js/hexagon-auth-login-state.js') }}?v=1" defer></script>
</head>
<body class="hx-account-body">
    <header class="hx-header">
        <div class="hx-header-inner">
            <a class="hx-brand" href="{{ url('/') }}" aria-label="HEXAGON">HEXAGON<span class="hx-brand-dot">.</span></a>
            <button type="button" class="hx-language-switch" data-hx-language-switch
                    aria-label="Switch to English" title="Switch to English">
                <img data-hx-flag
                     data-flag-en="{{ asset('images/flags/gb.svg') }}"
                     data-flag-vi="{{ asset('images/flags/vn.svg') }}"
                     src="{{ asset('images/flags/gb.svg') }}" alt="English">
            </button>
        </div>
    </header>

    <div class="hx-shell">
        <aside class="hx-sidebar" aria-label="Customer navigation">
            <div class="hx-sidebar-profile">
                <span class="hx-avatar">{{ mb_strtoupper(mb_substr(trim(Auth::user()->name), 0, 1)) }}</span>
                <div class="hx-side-identity">
                    <strong>{{ Auth::user()->name }}</strong>
                    <span>{{ Auth::user()->email }}</span>
                </div>
            </div>
            <div class="hx-sidebar-label" data-vi="KHU VỰC CỦA BẠN" data-en="YOUR SPACE">KHU VỰC CỦA BẠN</div>
            <nav class="hx-side-nav" aria-label="Khu vực khách hàng">
                <a href="{{ route('customer.account.index') }}"
                   class="hx-side-link {{ request()->routeIs('customer.account.index') || request()->routeIs('customer.account.profile.*') || request()->routeIs('customer.account.password.*') ? 'is-active' : '' }}"
                   {{ request()->routeIs('customer.account.index') ? 'aria-current=page' : '' }}>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4.5 21c0-4.2 3.2-7 7.5-7s7.5 2.8 7.5 7"/></svg>
                    <span data-vi="Tài khoản của tôi" data-en="My account">Tài khoản của tôi</span>
                </a>
                <a href="{{ route('customer.account.orders.index') }}"
                   class="hx-side-link {{ request()->routeIs('customer.account.orders.*') ? 'is-active' : '' }}"
                   {{ request()->routeIs('customer.account.orders.*') ? 'aria-current=page' : '' }}>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M8 5V3h8v2M7 11h10M7 15h7"/></svg>
                    <span data-vi="Đơn hàng của tôi" data-en="My orders">Đơn hàng của tôi</span>
                </a>
            </nav>
        </aside>
        <main id="main-content" class="hx-main">
            @yield('content')
        </main>
    </div>
</body>
</html>
