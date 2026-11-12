<header class="hx-header hx-shop-header">
    <div class="hx-container hx-header-inner">
        <a class="hx-brand" href="{{ url('/') }}" aria-label="HEXAGON — Trang chủ">HEXAGON<span class="hx-brand-dot">.</span></a>
        <nav class="hx-nav" aria-label="Điều hướng chính">
            <a href="{{ url('/') }}" class="hx-nav-link hx-hide-mobile" data-i18n="home">Trang chủ</a>
            @if (!request()->routeIs('cart.index'))
                <a href="{{ route('cart.index') }}" class="hx-nav-link hx-shop-cart">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1.5 2h3l2.8 12.2a2 2 0 0 0 2 1.5h9.3a2 2 0 0 0 2-1.6L22.5 6H5.7"/></svg>
                    <span data-i18n="cart">Giỏ hàng</span>
                </a>
            @endif
            @guest
                <a href="{{ route('customer.login') }}" class="hx-nav-link hx-shop-login">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10 17l5-5-5-5M15 12H3"/><path d="M12 3h6a3 3 0 0 1 3 3v12a3 3 0 0 1-3 3h-6"/></svg>
                    <span data-i18n="login">Đăng nhập</span>
                </a>
                <a href="{{ route('customer.register') }}" class="hx-nav-link hx-shop-register">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M20 8v6m-3-3h6"/></svg>
                    <span data-i18n="register">Đăng ký</span>
                </a>
                <details class="hx-shop-guest-menu" id="hexagonGuestMenu">
                    <summary aria-label="Mở menu tài khoản" data-i18n-aria="accountMenu">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>
                        <span data-i18n="accountShort">Tài khoản</span>
                    </summary>
                    <div class="hx-shop-guest-popover">
                        <a href="{{ route('customer.login') }}" data-i18n="login">Đăng nhập</a>
                        <a href="{{ route('customer.register') }}" data-i18n="register">Đăng ký</a>
                    </div>
                </details>
            @endguest
            <button class="hx-flag-button" id="hexagonLangToggle" type="button"
                    data-gb="{{ asset('images/flags/gb.svg') }}"
                    data-vn="{{ asset('images/flags/vn.svg') }}"
                    aria-label="Switch to English" title="Switch to English">
                <img id="hexagonLangFlag" src="{{ asset('images/flags/gb.svg') }}"
                     alt="" aria-hidden="true" width="28" height="28">
            </button>
            @auth
                <details class="hx-account" id="hexagonAccountMenu">
                    <summary class="hx-avatar" aria-label="Mở menu tài khoản" data-i18n-aria="accountMenu"
                             title="{{ auth()->user()->name }}">
                        {{ mb_strtoupper(mb_substr(trim((string) auth()->user()->name), 0, 1)) ?: 'H' }}
                    </summary>
                    <div class="hx-account-popover">
                        <div class="hx-account-identity">
                            <strong>{{ auth()->user()->name }}</strong>
                            <small>{{ auth()->user()->email }}</small>
                        </div>
                        @if(auth()->user()->is_admin)
                            <a href="{{ route('admin.dashboard') }}" data-i18n="admin">Trang quản trị</a>
                        @else
                            <a href="{{ route('customer.account.index') }}" data-i18n="myAccount">Tài khoản của tôi</a>
                            <a href="{{ route('customer.account.orders.index') }}" data-i18n="myOrders">Đơn hàng của tôi</a>
                        @endif
                        <div class="hx-account-separator"></div>
                        <form method="POST" action="{{ route(auth()->user()->is_admin ? 'admin.logout' : 'customer.logout') }}">
                            @csrf
                            <button type="submit" class="hx-signout" data-i18n="logout">Đăng xuất</button>
                        </form>
                    </div>
                </details>
            @endauth
        </nav>
    </div>
</header>
