<header class="hx-header">
    <div class="hx-container hx-header-inner">
        <a class="hx-brand" href="{{ url('/') }}" aria-label="HEXAGON — Home">HEXAGON</a>
        <nav class="hx-nav" aria-label="Main navigation">
            <a href="{{ url('/') }}" class="hx-nav-link hx-hide-mobile" data-i18n="home">Trang chủ</a>
                            @if (!request()->routeIs('cart.index'))
                    <a href="{{ route('cart.index') }}"
                    class="hx-nav-link"
                    data-i18n="cart">
                        Giỏ hàng
                    </a>
                @endif
            @guest
                <a href="{{ route('customer.login') }}" class="hx-nav-link" data-i18n="login">Đăng nhập</a>
            @endguest
            <span class="hx-nav-divider" aria-hidden="true"></span>
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
