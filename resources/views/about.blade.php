<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#08132b">
    <meta name="description" content="Khám phá câu chuyện, định hướng phát triển và các giải pháp số của HEXAGON — Công ty Cổ phần Lục Giác.">
    <title>Giới thiệu HEXAGON — Tên miền &amp; giải pháp số</title>
    <link rel="stylesheet" href="{{ asset('css/hexagon-web-polish.css') }}?v=5">
    <link rel="stylesheet" href="{{ asset('css/hexagon-about-premium.css') }}?v=1">
    <script src="{{ asset('js/hexagon-about-premium.js') }}?v=1" defer></script>
    <link rel="stylesheet" href="{{ asset('css/hexagon-unified-ui-v1.css') }}?v=1">
    <link rel="stylesheet" href="{{ asset('css/hexagon-header-cta-v2.css') }}?v=2">
    <script src="{{ asset('js/hexagon-unified-ui-v1.js') }}?v=1" defer></script>
</head>
@php
    $about = config('hexagon_about');
    $cart = session('hexagon_cart', []);
    $cartCount = is_array($cart) ? count($cart) : 0;
@endphp
<body class="ha-page">
<a class="ha-skip" href="#main-content" data-hx-vi="Đến nội dung chính" data-hx-en="Skip to content">Đến nội dung chính</a>

<header class="ha-header">
    <div class="ha-wrap ha-header-inner">
        <a class="ha-brand" href="{{ url('/') }}" aria-label="HEXAGON — Trang chủ">
            <svg viewBox="0 0 46 46" width="38" height="38" fill="none" aria-hidden="true"><path d="M23 2.8 41 13.2v19.6L23 43.2 5 32.8V13.2L23 2.8Z" fill="#3176E5" stroke="#9BCBFF" stroke-width="1.7"/><path d="M15 15v16m16-16v16M15 23h16" stroke="white" stroke-width="3.7" stroke-linecap="round"/></svg>
            <span>HEXAGON<span class="ha-dot">.</span></span>
        </a>
        <nav class="ha-navigation" id="haNavigation" aria-label="Điều hướng chính">
            <a href="{{ url('/') }}" data-hx-vi="Trang chủ" data-hx-en="Home">Trang chủ</a>
            <a class="ha-current" aria-current="page" href="{{ route('hexagon.about') }}" data-hx-vi="Giới thiệu" data-hx-en="About">Giới thiệu</a>
            <a href="{{ url('/') }}#domain-search" data-hx-vi="Tên miền" data-hx-en="Domains">Tên miền</a>
            @if(\Illuminate\Support\Facades\Route::has('saas.index'))
                <a href="{{ route('saas.index') }}" data-hx-vi="Giải pháp SaaS" data-hx-en="SaaS solutions">Giải pháp SaaS</a>
            @else
                <a href="{{ url('/') }}#solutions" data-hx-vi="Giải pháp SaaS" data-hx-en="SaaS solutions">Giải pháp SaaS</a>
            @endif
            @guest
                <a class="ha-mobile-login" href="{{ route('customer.login') }}" data-hx-vi="Đăng nhập" data-hx-en="Log in">Đăng nhập</a>
                <a class="ha-mobile-register" href="{{ route('customer.register') }}" data-hx-vi="Đăng ký" data-hx-en="Sign up">Đăng ký</a>
            @endguest
        </nav>
        <div class="ha-header-actions">
            <a class="ha-cart" href="{{ route('cart.index') }}" aria-label="Giỏ hàng" title="Giỏ hàng">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 2h3l3 14h13l2-10H5"/></svg>
                <span data-hx-vi="Giỏ hàng" data-hx-en="Cart">Giỏ hàng</span>
                @if($cartCount > 0)<small class="ha-cart-count">{{ $cartCount }}</small>@endif
            </a>
            @guest
                <a class="ha-login" href="{{ route('customer.login') }}" data-hx-vi="Đăng nhập" data-hx-en="Log in">Đăng nhập</a>
                <a class="ha-register" href="{{ route('customer.register') }}" data-hx-vi="Đăng ký" data-hx-en="Sign up">Đăng ký</a>
            @endguest
            @auth
                <details class="ha-account" id="haAccountMenu">
                    <summary class="ha-avatar" title="{{ auth()->user()->name }}" aria-label="Mở menu tài khoản">
                        {{ mb_strtoupper(mb_substr(trim((string) auth()->user()->name), 0, 1)) ?: 'H' }}
                    </summary>
                    <div class="ha-popover">
                        <strong>{{ auth()->user()->name }}</strong><small>{{ auth()->user()->email }}</small>
                        @if(auth()->user()->is_admin)
                            <a href="{{ route('admin.dashboard') }}" data-hx-vi="Trang quản trị" data-hx-en="Admin dashboard">Trang quản trị</a>
                        @else
                            <a href="{{ route('customer.account.index') }}" data-hx-vi="Tài khoản của tôi" data-hx-en="My account">Tài khoản của tôi</a>
                            <a href="{{ route('customer.account.orders.index') }}" data-hx-vi="Đơn hàng của tôi" data-hx-en="My orders">Đơn hàng của tôi</a>
                        @endif
                        <form method="POST" action="{{ route(auth()->user()->is_admin ? 'admin.logout' : 'customer.logout') }}">
                            @csrf
                            <button type="submit" data-hx-vi="Đăng xuất" data-hx-en="Sign out">Đăng xuất</button>
                        </form>
                    </div>
                </details>
            @endauth
            <button class="ha-language" id="haLangToggle" type="button" aria-label="Switch to English" title="Switch to English">
                <img id="haLangFlag" src="{{ asset('images/flags/gb.svg') }}" data-gb="{{ asset('images/flags/gb.svg') }}" data-vn="{{ asset('images/flags/vn.svg') }}" alt="" aria-hidden="true" width="26" height="26">
            </button>
            <button id="haMenuButton" class="ha-menu-button" type="button" aria-controls="haNavigation" aria-expanded="false" aria-label="Mở menu điều hướng"><span></span><span></span><span></span></button>
        </div>
    </div>
</header>

<main id="main-content">
    <section class="ha-hero">
        <div class="ha-hero-mesh" aria-hidden="true"></div>
        <div class="ha-wrap ha-hero-grid">
            <div class="ha-hero-copy">
                <span class="ha-eyebrow"><span class="ha-pulse" aria-hidden="true"></span><span data-hx-vi="{{ $about['hero']['eyebrow']['vi'] }}" data-hx-en="{{ $about['hero']['eyebrow']['en'] }}">{{ $about['hero']['eyebrow']['vi'] }}</span></span>
                <h1><span data-hx-vi="{{ $about['hero']['line_one']['vi'] }}" data-hx-en="{{ $about['hero']['line_one']['en'] }}">{{ $about['hero']['line_one']['vi'] }}</span><span class="ha-hero-em" data-hx-vi="{{ $about['hero']['line_two']['vi'] }}" data-hx-en="{{ $about['hero']['line_two']['en'] }}">{{ $about['hero']['line_two']['vi'] }}</span></h1>
                <p class="ha-hero-lead" data-hx-vi="{{ $about['hero']['description']['vi'] }}" data-hx-en="{{ $about['hero']['description']['en'] }}">{{ $about['hero']['description']['vi'] }}</p>
                <div class="ha-hero-actions">
                    <a class="ha-btn ha-btn-primary" href="{{ url('/') }}#domain-search"><span data-hx-vi="{{ $about['hero']['primary_action']['vi'] }}" data-hx-en="{{ $about['hero']['primary_action']['en'] }}">{{ $about['hero']['primary_action']['vi'] }}</span><span aria-hidden="true">↗</span></a>
                    <a class="ha-btn ha-btn-ghost" href="#direction"><span data-hx-vi="{{ $about['hero']['secondary_action']['vi'] }}" data-hx-en="{{ $about['hero']['secondary_action']['en'] }}">{{ $about['hero']['secondary_action']['vi'] }}</span><span aria-hidden="true">↓</span></a>
                </div>
                <div class="ha-hero-points">
                    @foreach($about['quick_points'] as $point)
                        <span><svg viewBox="0 0 20 20" aria-hidden="true" fill="none"><path d="m4 10 4 4 8-8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg><span data-hx-vi="{{ $point['vi'] }}" data-hx-en="{{ $point['en'] }}">{{ $point['vi'] }}</span></span>
                    @endforeach
                </div>
            </div>
            <div class="ha-hero-art" aria-hidden="true">
                <div class="ha-art-orbit ha-orbit-1"></div><div class="ha-art-orbit ha-orbit-2"></div>
                <div class="ha-art-sheen"></div>
                <svg class="ha-art-mark" viewBox="0 0 380 380" fill="none"><defs><linearGradient id="ha-gradient" x1="45" y1="55" x2="310" y2="338" gradientUnits="userSpaceOnUse"><stop stop-color="#79D6FF"/><stop offset=".55" stop-color="#437EF4"/><stop offset="1" stop-color="#73A2FF"/></linearGradient></defs><path d="M190 22 333 104.5v171L190 358 47 275.5v-171L190 22Z" fill="url(#ha-gradient)" fill-opacity=".12" stroke="url(#ha-gradient)" stroke-width="3"/><path d="M190 61 299 124.5v131L190 319 81 255.5v-131L190 61Z" fill="#101f47" stroke="#8AD8FF" stroke-width="2"/><path d="M139 144v92m102-92v92m-102-47h102" stroke="#E8F7FF" stroke-width="15" stroke-linecap="round"/><circle cx="190" cy="190" r="170" stroke="#6C9EEC" stroke-opacity=".26"/></svg>
                <span class="ha-orbit-tag ha-orbit-tag-a">DOMAIN</span><span class="ha-orbit-tag ha-orbit-tag-b">SAAS</span><span class="ha-orbit-tag ha-orbit-tag-c">HEXAGON</span>
            </div>
        </div>
    </section>

    <section class="ha-story ha-section" id="story">
        <div class="ha-wrap ha-story-grid">
            <div class="ha-section-heading">
                <span class="ha-section-tag" data-hx-vi="{{ $about['story']['eyebrow']['vi'] }}" data-hx-en="{{ $about['story']['eyebrow']['en'] }}">{{ $about['story']['eyebrow']['vi'] }}</span>
                <h2 data-hx-vi="{{ $about['story']['headline']['vi'] }}" data-hx-en="{{ $about['story']['headline']['en'] }}">{{ $about['story']['headline']['vi'] }}</h2>
                <div class="ha-heading-line"></div>
            </div>
            <div class="ha-story-text">
                <p data-hx-vi="{{ $about['story']['paragraph_one']['vi'] }}" data-hx-en="{{ $about['story']['paragraph_one']['en'] }}">{{ $about['story']['paragraph_one']['vi'] }}</p>
                <p data-hx-vi="{{ $about['story']['paragraph_two']['vi'] }}" data-hx-en="{{ $about['story']['paragraph_two']['en'] }}">{{ $about['story']['paragraph_two']['vi'] }}</p>
                <div class="ha-story-stamp"><span class="ha-stamp-icon" aria-hidden="true">H</span><span>HEXAGON <small>·</small> <span data-hx-vi="CÔNG TY CỔ PHẦN LỤC GIÁC" data-hx-en="LUC GIAC JOINT STOCK COMPANY">CÔNG TY CỔ PHẦN LỤC GIÁC</span></span></div>
            </div>
        </div>
    </section>

    <section class="ha-direction ha-section" id="direction">
        <div class="ha-wrap">
            <div class="ha-section-head-centered"><span class="ha-section-tag ha-section-tag-dark" data-hx-vi="{{ $about['vision']['eyebrow']['vi'] }}" data-hx-en="{{ $about['vision']['eyebrow']['en'] }}">{{ $about['vision']['eyebrow']['vi'] }}</span><h2 data-hx-vi="{{ $about['vision']['headline']['vi'] }}" data-hx-en="{{ $about['vision']['headline']['en'] }}">{{ $about['vision']['headline']['vi'] }}</h2></div>
            <div class="ha-direction-grid">
                @foreach(['mission', 'vision'] as $name)
                    @php($card = $about['vision'][$name])
                    <article class="ha-direction-card ha-direction-{{ $name }}"><span class="ha-card-num">0{{ $loop->iteration }}</span><span class="ha-card-label" data-hx-vi="{{ $card['label']['vi'] }}" data-hx-en="{{ $card['label']['en'] }}">{{ $card['label']['vi'] }}</span><h3 data-hx-vi="{{ $card['title']['vi'] }}" data-hx-en="{{ $card['title']['en'] }}">{{ $card['title']['vi'] }}</h3><p data-hx-vi="{{ $card['description']['vi'] }}" data-hx-en="{{ $card['description']['en'] }}">{{ $card['description']['vi'] }}</p></article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="ha-values ha-section" id="principles">
        <div class="ha-wrap">
            <div class="ha-values-head"><span class="ha-section-tag" data-hx-vi="{{ $about['values']['eyebrow']['vi'] }}" data-hx-en="{{ $about['values']['eyebrow']['en'] }}">{{ $about['values']['eyebrow']['vi'] }}</span><h2 data-hx-vi="{{ $about['values']['headline']['vi'] }}" data-hx-en="{{ $about['values']['headline']['en'] }}">{{ $about['values']['headline']['vi'] }}</h2></div>
            <div class="ha-values-grid">
                @foreach($about['values']['items'] as $item)
                    <article class="ha-value-card"><span class="ha-value-index">{{ sprintf('%02d', $loop->iteration) }}</span><span class="ha-value-decoration" aria-hidden="true"><span></span><span></span><span></span></span><h3 data-hx-vi="{{ $item['title']['vi'] }}" data-hx-en="{{ $item['title']['en'] }}">{{ $item['title']['vi'] }}</h3><p data-hx-vi="{{ $item['description']['vi'] }}" data-hx-en="{{ $item['description']['en'] }}">{{ $item['description']['vi'] }}</p></article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="ha-ecosystem ha-section" id="ecosystem">
        <div class="ha-wrap">
            <div class="ha-ecosystem-head"><div><span class="ha-section-tag" data-hx-vi="{{ $about['ecosystem']['eyebrow']['vi'] }}" data-hx-en="{{ $about['ecosystem']['eyebrow']['en'] }}">{{ $about['ecosystem']['eyebrow']['vi'] }}</span><h2 data-hx-vi="{{ $about['ecosystem']['headline']['vi'] }}" data-hx-en="{{ $about['ecosystem']['headline']['en'] }}">{{ $about['ecosystem']['headline']['vi'] }}</h2></div><p data-hx-vi="{{ $about['ecosystem']['description']['vi'] }}" data-hx-en="{{ $about['ecosystem']['description']['en'] }}">{{ $about['ecosystem']['description']['vi'] }}</p></div>
            <div class="ha-ecosystem-grid">
                <article class="ha-product ha-product-domain">
                    <div class="ha-product-icon" aria-hidden="true"><svg viewBox="0 0 48 48" fill="none"><circle cx="24" cy="24" r="17" stroke="currentColor" stroke-width="2.5"/><path d="M7 24h34M24 7c-6 6-8 27 0 34M24 7c6 6 8 27 0 34" stroke="currentColor" stroke-width="2.5"/></svg></div>
                    <span class="ha-product-tag" data-hx-vi="{{ $about['ecosystem']['domains']['tag']['vi'] }}" data-hx-en="{{ $about['ecosystem']['domains']['tag']['en'] }}">{{ $about['ecosystem']['domains']['tag']['vi'] }}</span>
                    <h3 data-hx-vi="{{ $about['ecosystem']['domains']['title']['vi'] }}" data-hx-en="{{ $about['ecosystem']['domains']['title']['en'] }}">{{ $about['ecosystem']['domains']['title']['vi'] }}</h3>
                    <p data-hx-vi="{{ $about['ecosystem']['domains']['description']['vi'] }}" data-hx-en="{{ $about['ecosystem']['domains']['description']['en'] }}">{{ $about['ecosystem']['domains']['description']['vi'] }}</p>
                    <a href="{{ url('/') }}#domain-search"><span data-hx-vi="{{ $about['ecosystem']['domains']['action']['vi'] }}" data-hx-en="{{ $about['ecosystem']['domains']['action']['en'] }}">{{ $about['ecosystem']['domains']['action']['vi'] }}</span><span aria-hidden="true">↗</span></a>
                </article>
                <article class="ha-product ha-product-saas">
                    <div class="ha-product-icon" aria-hidden="true"><svg viewBox="0 0 48 48" fill="none"><rect x="6" y="6" width="15" height="15" rx="4" stroke="currentColor" stroke-width="2.5"/><rect x="27" y="6" width="15" height="15" rx="4" stroke="currentColor" stroke-width="2.5"/><rect x="6" y="27" width="15" height="15" rx="4" stroke="currentColor" stroke-width="2.5"/><rect x="27" y="27" width="15" height="15" rx="4" stroke="currentColor" stroke-width="2.5"/></svg></div>
                    <span class="ha-product-tag" data-hx-vi="{{ $about['ecosystem']['saas']['tag']['vi'] }}" data-hx-en="{{ $about['ecosystem']['saas']['tag']['en'] }}">{{ $about['ecosystem']['saas']['tag']['vi'] }}</span>
                    <h3 data-hx-vi="{{ $about['ecosystem']['saas']['title']['vi'] }}" data-hx-en="{{ $about['ecosystem']['saas']['title']['en'] }}">{{ $about['ecosystem']['saas']['title']['vi'] }}</h3>
                    <p data-hx-vi="{{ $about['ecosystem']['saas']['description']['vi'] }}" data-hx-en="{{ $about['ecosystem']['saas']['description']['en'] }}">{{ $about['ecosystem']['saas']['description']['vi'] }}</p>
                    @if(\Illuminate\Support\Facades\Route::has('saas.index'))
                        <a href="{{ route('saas.index') }}"><span data-hx-vi="{{ $about['ecosystem']['saas']['action']['vi'] }}" data-hx-en="{{ $about['ecosystem']['saas']['action']['en'] }}">{{ $about['ecosystem']['saas']['action']['vi'] }}</span><span aria-hidden="true">↗</span></a>
                    @else
                        <a href="{{ url('/') }}#solutions"><span data-hx-vi="{{ $about['ecosystem']['saas']['action']['vi'] }}" data-hx-en="{{ $about['ecosystem']['saas']['action']['en'] }}">{{ $about['ecosystem']['saas']['action']['vi'] }}</span><span aria-hidden="true">↗</span></a>
                    @endif
                </article>
            </div>
        </div>
    </section>

    <section class="ha-close-section">
        <div class="ha-wrap ha-close-panel">
            <div><span class="ha-section-tag" data-hx-vi="{{ $about['closing']['eyebrow']['vi'] }}" data-hx-en="{{ $about['closing']['eyebrow']['en'] }}">{{ $about['closing']['eyebrow']['vi'] }}</span><h2 data-hx-vi="{{ $about['closing']['title']['vi'] }}" data-hx-en="{{ $about['closing']['title']['en'] }}">{{ $about['closing']['title']['vi'] }}</h2><p data-hx-vi="{{ $about['closing']['description']['vi'] }}" data-hx-en="{{ $about['closing']['description']['en'] }}">{{ $about['closing']['description']['vi'] }}</p></div>
            <a class="ha-btn ha-btn-primary" href="{{ url('/') }}"><span data-hx-vi="{{ $about['closing']['action']['vi'] }}" data-hx-en="{{ $about['closing']['action']['en'] }}">{{ $about['closing']['action']['vi'] }}</span><span aria-hidden="true">↗</span></a>
        </div>
    </section>
</main>
@include('partials.hexagon-premium-footer')
</body>
</html>
