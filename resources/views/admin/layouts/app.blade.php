<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#101d36">
    <title>@yield('title', 'Quản trị - HEXAGON')</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}?v=3">
    <link rel="stylesheet" href="{{ asset('css/hexagon-web-polish.css') }}?v=4">
    <link rel="stylesheet" href="{{ asset('css/hexagon-unified-ui-v1.css') }}?v=1">
    @stack('styles')
    <link rel="stylesheet" href="{{ asset('css/hexagon-admin-pro-v1.css') }}?v=1">
    <script src="{{ asset('js/hexagon-admin-pro-v1.js') }}?v=1" defer></script>
    @stack('scripts')
    <!-- HEXAGON_FONT_SOURCE_SANS_V1_START -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@400;500;600;700;800&amp;display=swap">
    <link rel="stylesheet" href="{{ asset('css/hexagon-font-source-sans-v1.css') }}?v=1">
    <!-- HEXAGON_FONT_SOURCE_SANS_V1_END -->
</head>
<body class="hx-admin-v3 hx-admin-pro">
<a class="hx-skip-link" href="#admin-main">Bỏ qua menu, đến nội dung chính</a>
<svg class="hx-icon-defs" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
    <symbol id="hx-ico-grid" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></symbol>
    <symbol id="hx-ico-orders" viewBox="0 0 24 24"><rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 8h8M8 12h8M8 16h5"/></symbol>
    <symbol id="hx-ico-domain" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c-3 3-4 6-4 9s1 6 4 9M12 3c3 3 4 6 4 9s-1 6-4 9"/></symbol>
    <symbol id="hx-ico-users" viewBox="0 0 24 24"><circle cx="9" cy="8" r="3"/><path d="M3 20v-2a6 6 0 0 1 12 0v2M17 5a3 3 0 0 1 0 6M18 15a5 5 0 0 1 3 5"/></symbol>
    <symbol id="hx-ico-saas" viewBox="0 0 24 24"><rect x="3" y="3" width="8" height="8" rx="2"/><rect x="13" y="3" width="8" height="8" rx="2"/><rect x="3" y="13" width="8" height="8" rx="2"/><path d="M17 13v8M13 17h8"/></symbol>
    <symbol id="hx-ico-external" viewBox="0 0 24 24"><path d="M13 5h6v6M19 5l-9 9"/><path d="M19 13v5a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h5"/></symbol>
    <symbol id="hx-ico-menu" viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h16"/></symbol>
    <symbol id="hx-ico-search" viewBox="0 0 24 24"><circle cx="10.8" cy="10.8" r="7"/><path d="m16 16 5 5"/></symbol>
    <symbol id="hx-ico-chevron" viewBox="0 0 24 24"><path d="m9 6 6 6-6 6"/></symbol>
    <symbol id="hx-ico-arrow" viewBox="0 0 24 24"><path d="M4 12h16m-7-7 7 7-7 7"/></symbol>
    <symbol id="hx-ico-logout" viewBox="0 0 24 24"><path d="M10 4H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h4M14 16l4-4-4-4M18 12H9"/></symbol>
    <symbol id="hx-ico-close" viewBox="0 0 24 24"><path d="M5 5l14 14M19 5 5 19"/></symbol>
    <symbol id="hx-ico-trend" viewBox="0 0 24 24"><path d="m3 17 7-7 4 4 7-7M15 7h6v6"/></symbol>
    <symbol id="hx-ico-clock" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></symbol>
</svg>
<div class="admin-layout" data-admin-shell>
    <div class="hx-sidebar-backdrop" data-sidebar-close hidden></div>
    <aside class="admin-sidebar" id="admin-sidebar" aria-label="Menu quản trị">
        <a class="hx-brand" href="{{ route('admin.dashboard') }}" title="HEXAGON - Bảng điều khiển">
            <span class="hx-brand-logo" aria-hidden="true">H</span>
            <span class="hx-brand-copy"><strong>HEXAGON<span class="hx-brand-dot">.</span></strong><small>TRUNG TÂM QUẢN TRỊ</small></span>
        </a>
        <div class="hx-sidebar-separator"></div>
        <nav class="admin-nav" aria-label="Điều hướng chính">
            <div class="hx-nav-section"><span class="hx-nav-label">TỔNG QUAN</span>
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}" @if(request()->routeIs('admin.dashboard')) aria-current="page" @endif title="Tổng quan">
                    <svg class="hx-icon"><use href="#hx-ico-grid"/></svg><span class="hx-nav-text">Bảng điều khiển</span><svg class="hx-icon hx-nav-arrow"><use href="#hx-ico-chevron"/></svg>
                </a>
            </div>
            <div class="hx-nav-section"><span class="hx-nav-label">KINH DOANH</span>
                <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}" @if(request()->routeIs('admin.orders.*')) aria-current="page" @endif title="Đơn hàng">
                    <svg class="hx-icon"><use href="#hx-ico-orders"/></svg><span class="hx-nav-text">Đơn hàng</span><svg class="hx-icon hx-nav-arrow"><use href="#hx-ico-chevron"/></svg>
                </a>
                <a class="nav-link {{ request()->routeIs('admin.domain-prices.*') ? 'active' : '' }}" href="{{ route('admin.domain-prices.index') }}" @if(request()->routeIs('admin.domain-prices.*')) aria-current="page" @endif title="Bảng giá tên miền">
                    <svg class="hx-icon"><use href="#hx-ico-domain"/></svg><span class="hx-nav-text">Bảng giá tên miền</span><svg class="hx-icon hx-nav-arrow"><use href="#hx-ico-chevron"/></svg>
                </a>
                <a class="nav-link {{ request()->routeIs('admin.saas.*') ? 'active' : '' }}" href="{{ route('admin.saas.index') }}" @if(request()->routeIs('admin.saas.*')) aria-current="page" @endif title="Dịch vụ SaaS">
                    <svg class="hx-icon"><use href="#hx-ico-saas"/></svg><span class="hx-nav-text">Dịch vụ SaaS</span><svg class="hx-icon hx-nav-arrow"><use href="#hx-ico-chevron"/></svg>
                </a>
            </div>
            <div class="hx-nav-section"><span class="hx-nav-label">KHÁCH HÀNG</span>
                <a class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}" href="{{ route('admin.customers.index') }}" @if(request()->routeIs('admin.customers.*')) aria-current="page" @endif title="Khách hàng">
                    <svg class="hx-icon"><use href="#hx-ico-users"/></svg><span class="hx-nav-text">Khách hàng</span><svg class="hx-icon hx-nav-arrow"><use href="#hx-ico-chevron"/></svg>
                </a>
            </div>
        </nav>
        <div class="hx-sidebar-footer">
            <a class="hx-website-link" href="{{ url('/') }}" target="_blank" rel="noopener noreferrer"><svg class="hx-icon"><use href="#hx-ico-external"/></svg><span class="hx-nav-text">Xem trang bán hàng</span></a>
            <div class="hx-sidebar-support"><span class="hx-status-dot" aria-hidden="true"></span><span class="hx-nav-text">Không gian quản trị HEXAGON</span></div>
        </div>
    </aside>

    <div class="admin-content">
        <header class="admin-topbar">
            <div class="hx-topbar-left">
                <button type="button" class="hx-icon-button hx-mobile-menu" data-sidebar-toggle aria-label="Mở menu quản trị" aria-controls="admin-sidebar" aria-expanded="false"><svg class="hx-icon"><use href="#hx-ico-menu"/></svg></button>
                <button type="button" class="hx-icon-button hx-collapse-sidebar" data-sidebar-collapse aria-label="Thu gọn hoặc mở rộng menu" title="Thu gọn / mở rộng menu"><svg class="hx-icon"><use href="#hx-ico-menu"/></svg></button>
                <div class="hx-topbar-titles"><div class="hx-breadcrumb"><a href="{{ route('admin.dashboard') }}">HEXAGON</a><svg class="hx-icon"><use href="#hx-ico-chevron"/></svg><span>@yield('topbar_title', 'Bảng điều khiển')</span></div><strong>@yield('topbar_title', 'Bảng điều khiển')</strong></div>
            </div>
            <div class="hx-topbar-right">
                <button type="button" class="hx-search-trigger" data-search-open title="Tìm nhanh (Ctrl + K)"><svg class="hx-icon"><use href="#hx-ico-search"/></svg><span>Tìm chức năng...</span><kbd>Ctrl K</kbd></button>
                <a class="hx-topbar-site" href="{{ url('/') }}" target="_blank" rel="noopener noreferrer">Xem website <svg class="hx-icon"><use href="#hx-ico-external"/></svg></a>
                <details class="hx-admin-account"><summary aria-label="Tài khoản quản trị"><span class="hx-admin-avatar">{{ mb_strtoupper(mb_substr(trim((string) (auth('admin')->user()?->name ?? 'A')), 0, 1)) }}</span><span class="hx-admin-account-label"><strong>{{ auth('admin')->user()?->name ?? 'Quản trị viên' }}</strong><small>Quản trị viên</small></span><svg class="hx-icon hx-account-chevron"><use href="#hx-ico-chevron"/></svg></summary><div class="hx-account-dropdown"><div class="hx-account-name">{{ auth('admin')->user()?->email ?? 'Tài khoản quản trị' }}</div><form method="POST" action="{{ route('admin.logout') }}">@csrf<button type="submit"><svg class="hx-icon"><use href="#hx-ico-logout"/></svg> Đăng xuất</button></form></div></details>
            </div>
        </header>
        <main class="admin-main" id="admin-main" tabindex="-1">@yield('content')</main>
    </div>
</div>
<div class="hx-quick-search" data-quick-search hidden>
    <div class="hx-quick-search-shade" data-search-close></div>
    <div class="hx-quick-search-box" role="dialog" aria-modal="true" aria-labelledby="hx-quick-title">
        <div class="hx-quick-header"><svg class="hx-icon"><use href="#hx-ico-search"/></svg><label class="hx-visually-hidden" for="hx-quick-input" id="hx-quick-title">Tìm chức năng quản trị</label><input id="hx-quick-input" type="search" placeholder="Tìm đơn hàng, khách hàng, bảng giá..." autocomplete="off"><button type="button" class="hx-icon-button" data-search-close aria-label="Đóng tìm kiếm"><svg class="hx-icon"><use href="#hx-ico-close"/></svg></button></div>
        <div class="hx-quick-results" data-search-results>
            <a href="{{ route('admin.dashboard') }}" data-search-item data-keywords="dashboard tổng quan thống kê"><svg class="hx-icon"><use href="#hx-ico-grid"/></svg><span>Bảng điều khiển</span><small>Đi tới</small></a>
            <a href="{{ route('admin.orders.index') }}" data-search-item data-keywords="đơn hàng đặt hàng xử lý"><svg class="hx-icon"><use href="#hx-ico-orders"/></svg><span>Đơn hàng</span><small>Đi tới</small></a>
            <a href="{{ route('admin.domain-prices.index') }}" data-search-item data-keywords="bảng giá tên miền thêm sửa đuôi"><svg class="hx-icon"><use href="#hx-ico-domain"/></svg><span>Bảng giá tên miền</span><small>Đi tới</small></a>
            <a href="{{ route('admin.saas.index') }}" data-search-item data-keywords="dịch vụ saas sản phẩm phần mềm"><svg class="hx-icon"><use href="#hx-ico-saas"/></svg><span>Dịch vụ SaaS</span><small>Đi tới</small></a>
            <a href="{{ route('admin.customers.index') }}" data-search-item data-keywords="khách hàng tài khoản người dùng"><svg class="hx-icon"><use href="#hx-ico-users"/></svg><span>Khách hàng</span><small>Đi tới</small></a>
        </div>
        <div class="hx-quick-footer">Nhấn <kbd>Esc</kbd> để đóng · <kbd>Enter</kbd> để mở kết quả đầu tiên</div>
    </div>
</div>
</body>
</html>
