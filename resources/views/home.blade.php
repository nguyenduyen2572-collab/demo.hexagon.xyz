<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#08132b">
    <meta name="description" content="HEXAGON — Tìm kiếm tên miền và khám phá giải pháp dịch vụ số cho doanh nghiệp.">
    <title>HEXAGON — Tên miền &amp; giải pháp số cho tương lai</title>
    <link rel="stylesheet" href="{{ asset('css/hexagon-home-premium.css') }}?v=2">
    <script src="{{ asset('js/hexagon-home-premium.js') }}?v=2" defer></script>
    <link rel="stylesheet" href="{{ asset('css/hexagon-web-polish.css') }}?v=4">
    <script src="{{ asset('js/hexagon-web-polish.js') }}?v=4" defer></script>
    <link rel="stylesheet" href="{{ asset('css/hexagon-home-content-v6.css') }}?v=6">
    {{-- HEXAGON: gợi ý tên miền và giới hạn tìm kiếm 3 giây --}}
    <link rel="stylesheet" href="{{ asset('css/hexagon-domain-suggest-v1.css') }}?v=1">
    <script src="{{ asset('js/hexagon-domain-suggest-v1.js') }}?v=1" defer></script>
</head>
<body data-check-url="{{ url('/api/v1/domains/check') }}"
      data-add-cart-url="{{ url('/cart/items') }}">
    @php
        $cart = session('hexagon_cart', []);
        $cartCount = is_array($cart) ? count($cart) : 0;
    @endphp

    <a class="hx-skip-link" href="#mainContent" data-i18n="skip">Bỏ qua điều hướng</a>

    <header class="hx-header" id="top">
        <div class="hx-container hx-header-inner">
            <a class="hx-brand" href="{{ url('/') }}" aria-label="HEXAGON — Trang chủ">
                <span class="hx-brand-icon" aria-hidden="true">
                    <svg viewBox="0 0 42 42" fill="none"><path d="M21 2.9 36.7 12v18L21 39.1 5.3 30V12L21 2.9Z" fill="url(#hxBrandGradient)"/><path d="M14 13.5v15m14-15v15M14 21h14" stroke="white" stroke-width="3.2" stroke-linecap="round"/><defs><linearGradient id="hxBrandGradient" x1="4" y1="1" x2="40" y2="41"><stop stop-color="#82C7FF"/><stop offset="1" stop-color="#4777FF"/></linearGradient></defs></svg>
                </span>
                <span>HEXAGON<span class="hx-brand-dot">.</span></span>
            </a>

            <nav class="hx-main-nav" id="mainNav" aria-label="Điều hướng chính" data-i18n-aria="navigation">
                <a href="#domain-search" data-i18n="navDomain">Tên miền</a>
                @if(\Illuminate\Support\Facades\Route::has('saas.index'))
                    <a href="{{ route('saas.index') }}" data-i18n="navSaas">Giải pháp SaaS</a>
                @else
                    <a href="#solutions" data-i18n="navSaas">Giải pháp SaaS</a>
                @endif
                <a href="#pricing" data-i18n="navPricing">Bảng giá</a>
                <a href="#how-it-works" data-i18n="navGuide">Cách hoạt động</a>
                <a href="#support" data-i18n="navSupport">Hỗ trợ</a>
                @guest
                    <a class="hx-mobile-auth" href="{{ route('customer.login') }}" data-i18n="login">Đăng nhập</a>
@endguest
            </nav>

            <div class="hx-header-actions">
                <a class="hx-cart-link" href="{{ route('cart.index') }}" aria-label="Giỏ hàng" data-i18n-aria="cartLabel">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1.5 2h3l2.8 12.2a2 2 0 0 0 2 1.5h9.3a2 2 0 0 0 2-1.6L22.5 6H5.7"/></svg>
                    <span class="hx-cart-text" data-i18n="cart">Giỏ hàng</span>
                    <span id="cartCount" class="hx-cart-count" @if($cartCount === 0) hidden @endif>{{ $cartCount }}</span>
                </a>
                <button class="hx-language" id="langSwitch" type="button" aria-label="Switch to English" title="Switch to English">
                    <img id="langFlag" src="{{ asset('images/flags/gb.svg') }}" data-gb="{{ asset('images/flags/gb.svg') }}" data-vn="{{ asset('images/flags/vn.svg') }}" alt="" width="27" height="27" aria-hidden="true">
                </button>
                @guest
                    <a class="hx-header-login" href="{{ route('customer.login') }}" data-i18n="login">Đăng nhập</a>
@endguest
                @auth
                    <details class="hx-account" id="accountDropdown">
                        <summary class="hx-account-avatar" title="{{ auth()->user()->name }}" data-i18n-aria="accountMenu" aria-label="Mở menu tài khoản">
                            {{ mb_strtoupper(mb_substr(trim((string) auth()->user()->name), 0, 1)) ?: 'H' }}
                        </summary>
                        <div class="hx-account-popover">
                            <div class="hx-account-person">
                                <strong>{{ auth()->user()->name }}</strong>
                                <small>{{ auth()->user()->email }}</small>
                            </div>
                            <div class="hx-menu-divider"></div>
                            @if(auth()->user()->is_admin)
                                <a href="{{ route('admin.dashboard') }}" data-i18n="admin">Trang quản trị</a>
                            @else
                                <a href="{{ route('customer.account.index') }}" data-i18n="myAccount">Tài khoản của tôi</a>
                                <a href="{{ route('customer.account.orders.index') }}" data-i18n="myOrders">Đơn hàng của tôi</a>
                            @endif
                            <div class="hx-menu-divider"></div>
                            <form action="{{ route(auth()->user()->is_admin ? 'admin.logout' : 'customer.logout') }}" method="POST">
                                @csrf
                                <button type="submit" data-i18n="logout">Đăng xuất</button>
                            </form>
                        </div>
                    </details>
                @endauth
                <button class="hx-menu-toggle" type="button" id="mobileMenuButton" aria-controls="mainNav" aria-expanded="false" aria-label="Mở menu" data-i18n-aria="openMenu">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </div>
    </header>

    <main id="mainContent">
        <section class="hx-hero" id="domain-search" aria-labelledby="heroTitle">
            <div class="hx-hero-grid hx-container">
                <div class="hx-hero-content">
                    <div class="hx-eyebrow"><span class="hx-eyebrow-pulse" aria-hidden="true"></span><span data-i18n="eyebrow">NỀN TẢNG DỊCH VỤ SỐ HEXAGON</span></div>
                    <h1 id="heroTitle"><span data-i18n="heroLine1">Ý tưởng lớn bắt đầu</span><span class="hx-hero-accent" data-i18n="heroLine2">từ một tên miền.</span></h1>
                    <p class="hx-hero-copy" data-i18n="heroDescription">Tìm tên miền cho thương hiệu của bạn, quản lý đơn hàng dễ dàng và sẵn sàng khám phá hệ sinh thái SaaS đang được phát triển tại HEXAGON.</p>
                    <div class="hx-search-surface">
                        <form id="domainForm" class="hx-search-form" role="search">
                            <label class="hx-search-label" for="domainInput" data-i18n="domainSearchLabel">Tìm tên miền của bạn</label>
                            <div class="hx-search-field">
                                <span class="hx-search-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 5 5"/></svg></span>
                                <input id="domainInput" name="domain" type="text" maxlength="190" autocomplete="off" spellcheck="false" required placeholder="vd: thuonghieu.com" data-i18n-placeholder="domainPlaceholder" data-i18n-aria="domainAria" aria-label="Nhập tên miền cần kiểm tra">
                                <button id="searchButton" type="submit"><span data-i18n="search">Kiểm tra</span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg></button>
                            </div>
                        </form>
                        <div class="hx-search-subrow">
                            <span class="hx-demo-marker"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 11v5m0-8v.5"/></svg><span data-i18n="demoDisclaimer">Tra cứu sử dụng dữ liệu demo, chưa đăng ký thật.</span></span>
                        </div>
                        <div id="result" class="hx-search-result" aria-live="polite" hidden>
                            <div class="hx-result-head">
                                <div class="hx-result-main">
                                    <span class="hx-result-caption" data-i18n="resultFor">Kết quả tra cứu</span>
                                    <strong id="resultDomain" class="hx-result-domain"></strong>
                                </div>
                                <span id="resultStatus" class="hx-status-chip" hidden><span class="hx-status-dot" aria-hidden="true"></span><span id="resultStatusText"></span></span>
                            </div>
                            <p id="resultMessage" class="hx-result-message" hidden></p>
                            <div id="resultDetails" class="hx-result-details" hidden>
                                <div><span data-i18n="registrationPrice">Giá đăng ký</span><strong id="resultRegisterPrice"></strong></div>
                                <div><span data-i18n="renewalPrice">Giá gia hạn</span><strong id="resultRenewPrice"></strong></div>
                            </div>
                            <div id="resultActions" class="hx-result-actions" hidden>
                                <button id="addToCartBtn" class="hx-add-cart" type="button"><span data-i18n="addToCart">Thêm vào giỏ</span><span aria-hidden="true">→</span></button>
                                <a id="viewCartLink" href="{{ route('cart.index') }}" class="hx-view-cart" data-i18n="viewCart" hidden>Xem giỏ hàng</a>
                                <span id="cartMessage" role="status"></span>
                            </div>
                        </div>
                    </div>
                    <div class="hx-hero-points" aria-label="Các lợi ích">
                        <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m4 12 5 5L20 6"/></svg><span data-i18n="point1">Tra cứu nhanh</span></span>
                        <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m4 12 5 5L20 6"/></svg><span data-i18n="point2">Bảng giá rõ ràng</span></span>
                        <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m4 12 5 5L20 6"/></svg><span data-i18n="point3">Đặt hàng thuận tiện</span></span>
                    </div>
                </div>

                <div class="hx-hero-art" aria-hidden="true">
                    <div class="hx-art-glow"></div>
                    <div class="hx-art-orbit hx-art-orbit-one"></div>
                    <div class="hx-art-orbit hx-art-orbit-two"></div>
                    <div class="hx-hex-figure"><svg viewBox="0 0 220 220" fill="none"><path d="M110 11 194 60.5v99L110 209l-84-49.5v-99L110 11Z" fill="url(#hexFill)" stroke="url(#hexEdge)" stroke-width="2"/><path d="M79 77v66m62-66v66M79 110h62" stroke="#D9F8FF" stroke-width="13" stroke-linecap="round"/><defs><linearGradient id="hexFill" x1="20" y1="25" x2="195" y2="210"><stop stop-color="#73C5FF" stop-opacity=".32"/><stop offset="1" stop-color="#6274FF" stop-opacity=".12"/></linearGradient><linearGradient id="hexEdge" x1="0" y1="0" x2="220" y2="220"><stop stop-color="#9DEFFF"/><stop offset="1" stop-color="#647EFF"/></linearGradient></defs></svg></div>
                    <div class="hx-preview-card hx-preview-main">
                        <div class="hx-preview-toolbar"><span class="hx-window-lights"><i></i><i></i><i></i></span><span>hexagon / domain<span class="hx-preview-caret">_</span></span><span class="hx-preview-live"><b></b> DEMO</span></div>
                        <div class="hx-preview-content">
                            <div class="hx-preview-kicker">DOMAIN DISCOVERY</div>
                            <div class="hx-preview-title">yourbrand<span>.com</span></div>
                            <div class="hx-preview-progress"><span></span></div>
                            <div class="hx-preview-label">DOMAIN <span>·</span> SAAS <span>·</span> DIGITAL</div>
                            <div class="hx-preview-divider"></div>
                            <div class="hx-preview-row"><div class="hx-preview-mini-icon">⌁</div><div><b>Domain search</b><small>Browse &amp; discover</small></div><span class="hx-preview-arrow">↗</span></div>
                            <div class="hx-preview-row"><div class="hx-preview-mini-icon hx-purple">◈</div><div><b>SaaS ecosystem</b><small>Coming together</small></div><span class="hx-preview-arrow">↗</span></div>
                        </div>
                    </div>
                    <div class="hx-floating-card hx-floating-top"><span class="hx-float-icon">✦</span><span>Build your digital identity</span></div>
                    <div class="hx-floating-card hx-floating-bottom"><span class="hx-floating-online"></span><span>HEXAGON / DIGITAL FIRST</span></div>
                </div>
            </div>
            <div class="hx-hero-grid-bg" aria-hidden="true"></div>
        </section>

        <section class="hx-pricing-section" id="pricing" aria-labelledby="pricingTitle">
            <div class="hx-container">
                <div class="hx-section-top">
                    <div>
                        <span class="hx-section-kicker" data-i18n="pricingEyebrow">BẢNG GIÁ TÊN MIỀN</span>
                        <h2 id="pricingTitle"><span data-i18n="pricingTitle">Chọn đuôi tên miền.</span> <span class="hx-muted-title" data-i18n="pricingTitle2">Tỏa sáng theo cách của bạn.</span></h2>
                        <p data-i18n="pricingDescription">Mức giá được cập nhật từ hệ thống quản trị HEXAGON. Chọn một đuôi tên miền để bắt đầu tìm kiếm.</p>
                    </div>
                    <a class="hx-text-link" href="#domain-search"><span data-i18n="pricingCta">Tìm tên miền ngay</span><span aria-hidden="true">↗</span></a>
                </div>
                <div class="hx-price-grid">
                    @forelse ($prices as $price)
                        <button type="button" class="hx-price-card" data-extension="{{ $price->extension }}" title="{{ $price->extension }}">
                            <span class="hx-price-card-top"><span class="hx-ext-badge" data-i18n="priceLabel">Tên miền</span><span class="hx-price-arrow" aria-hidden="true">↗</span></span>
                            <span class="hx-price-extension">{{ $price->extension }}</span>
                            <span class="hx-price-caption" data-i18n="firstYear">Đăng ký năm đầu</span>
                            <strong class="hx-price-amount" data-money="{{ $price->registration_price }}">{{ number_format($price->registration_price, 0, ',', '.') }}đ</strong>
                            <span class="hx-price-renew"><span data-i18n="renewal">Gia hạn</span> <span data-money="{{ $price->renewal_price }}">{{ number_format($price->renewal_price, 0, ',', '.') }}đ</span><span data-i18n="perYear">/năm</span></span>
                        </button>
                    @empty
                        <p class="hx-price-empty" data-i18n="emptyPrices">Hiện chưa có bảng giá tên miền được công bố.</p>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="hx-solutions" id="solutions" aria-labelledby="solutionsTitle">
            <div class="hx-container">
                <div class="hx-section-top">
                    <div>
                        <span class="hx-section-kicker" data-i18n="solutionsEyebrow">HỆ SINH THÁI HEXAGON</span>
                        <h2 id="solutionsTitle"><span data-i18n="solutionsTitle">Một điểm khởi đầu.</span> <span class="hx-muted-title" data-i18n="solutionsTitle2">Nhiều cơ hội phát triển.</span></h2>
                        <p data-i18n="solutionsDescription">Bắt đầu với tên miền và khám phá danh mục giải pháp SaaS đang được mở rộng.</p>
                    </div>
                </div>
                <div class="hx-solution-grid">
                    <article class="hx-solution-card hx-solution-domain">
                        <div class="hx-solution-card-content">
                            <span class="hx-solution-tag"><span class="hx-solution-tag-icon" aria-hidden="true">◉</span> DOMAIN</span>
                            <h3 data-i18n="solutionDomainTitle">Tên miền là bước đầu tiên của thương hiệu.</h3>
                            <p data-i18n="solutionDomainDesc">Tra cứu, so sánh giá và thêm tên miền vào giỏ hàng chỉ trong vài thao tác.</p>
                            <a href="#domain-search" class="hx-solution-action"><span data-i18n="solutionDomainCta">Khám phá tên miền</span> <span aria-hidden="true">↗</span></a>
                        </div>
                        <div class="hx-domain-visual" aria-hidden="true"><div class="hx-domain-url"><span>⌘</span> https://<b>yourbrand.com</b><span class="hx-domain-url-dot"></span></div><div class="hx-domain-visual-rows"><span></span><span></span><span></span></div><div class="hx-domain-wave">WWW <span>↗</span></div></div>
                    </article>
                    <article class="hx-solution-card hx-solution-saas">
                        <div class="hx-solution-card-content">
                            <span class="hx-solution-tag"><span class="hx-solution-tag-icon" aria-hidden="true">✦</span> SAAS</span>
                            <h3 data-i18n="solutionSaasTitle">Công cụ số cho hành trình tiếp theo.</h3>
                            <p data-i18n="solutionSaasDesc">Tìm hiểu các dịch vụ SaaS trong danh mục đang được HEXAGON phát triển.</p>
                            @if(\Illuminate\Support\Facades\Route::has('saas.index'))
                                <a href="{{ route('saas.index') }}" class="hx-solution-action"><span data-i18n="solutionSaasCta">Xem danh mục SaaS</span> <span aria-hidden="true">↗</span></a>
                            @else
                                <span class="hx-solution-action hx-soon"><span data-i18n="solutionSaasSoon">Danh mục đang phát triển</span><span aria-hidden="true">✦</span></span>
                            @endif
                        </div>
                        <div class="hx-saas-visual" aria-hidden="true"><div class="hx-saas-tile hx-tile-one">▦</div><div class="hx-saas-tile hx-tile-two">✳</div><div class="hx-saas-tile hx-tile-three">⌘</div><div class="hx-saas-grid"></div></div>
                    </article>
                </div>
            </div>
        </section>

        @include('partials.hexagon-home-steps-v6')

        @include('partials.hexagon-home-faq-v6')

</main>

    @include('partials.hexagon-premium-footer')
</body>
</html>
