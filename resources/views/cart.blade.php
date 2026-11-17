<!doctype html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="theme-color" content="#102e5b">
    <title>Giỏ hàng - HEXAGON</title>
    <link rel="stylesheet" href="{{ asset('css/hexagon-shop.css') }}?v=1">
    <script src="{{ asset('js/hexagon-shop-i18n.js') }}?v=2" defer></script>
    <link rel="stylesheet" href="{{ asset('css/hexagon-web-polish.css') }}?v=4">
    <script src="{{ asset('js/hexagon-web-polish.js') }}?v=4" defer></script>
    <link rel="stylesheet" href="{{ asset('css/hexagon-unified-ui-v1.css') }}?v=1">
    <link rel="stylesheet" href="{{ asset('css/hexagon-header-cta-v2.css') }}?v=2">
    <script src="{{ asset('js/hexagon-unified-ui-v1.js') }}?v=1" defer></script>
    <!-- HEXAGON_FONT_SOURCE_SANS_V1_START -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@400;500;600;700;800&amp;display=swap">
    <link rel="stylesheet" href="{{ asset('css/hexagon-font-source-sans-v1.css') }}?v=1">
    <!-- HEXAGON_FONT_SOURCE_SANS_V1_END -->
    <link rel="stylesheet" href="{{ asset('css/hexagon-buyer-ux-v6.css') }}?v=6">
</head>
<body class="hx-buy-v6 hx-buy-cart" data-page-title="pageCart">
    @include('partials.hexagon-shop-header')
    <main class="hx-main hx-container" id="mainContent">
        <nav class="hx-buy-progress" aria-label="Các bước đặt hàng" data-i18n-aria="orderStepsAria">
            <span class="is-done"><span class="hx-progress-num">✓</span><span data-i18n="stepFind">Tìm tên miền</span></span>
            <span class="is-active" aria-current="step"><span class="hx-progress-num">2</span><span data-i18n="stepCart">Giỏ hàng</span></span>
            <span><span class="hx-progress-num">3</span><span data-i18n="stepCheckout">Đặt hàng</span></span>
        </nav>
        <div class="hx-intro hx-buy-intro">
            <div>
                <span class="hx-buy-eyebrow" data-i18n="cartEyebrow">BƯỚC 2 / 3</span>
                <h1 data-i18n="cartTitle">Giỏ hàng của bạn</h1>
                <p data-i18n="cartSubtitle">Xem lại tên miền và chi phí trước khi tiếp tục.</p>
            </div>
            <a class="hx-buy-text-link" href="{{ url('/') }}#domain-search" data-i18n="continueShopping">Tìm thêm tên miền</a>
        </div>
        @if(session('error'))
            <div class="hx-alert" role="alert">{{ session('error') }}</div>
        @endif
        @if(count($items) > 0)
            <div class="hx-buy-cart-layout">
                <section class="hx-card hx-buy-items" aria-labelledby="hxCartItemsTitle">
                    <div class="hx-buy-card-head">
                        <h2 id="hxCartItemsTitle" data-i18n="cartListTitle">Tên miền đã chọn</h2>
                        <span class="hx-buy-count">{{ count($items) }} <span data-i18n="domainCount">tên miền</span></span>
                    </div>
                    @foreach($items as $item)
                        <div class="hx-cart-item">
                            <div><div class="hx-domain">{{ $item['domain'] }}</div><div class="hx-meta" data-i18n="itemOneYear">Đăng ký 1 năm</div></div>
                            <div class="hx-cart-right">
                                <div class="hx-money" data-money="{{ $item['registration_price'] }}">{{ number_format($item['registration_price'], 0, ',', '.') }}đ</div>
                                <form action="{{ route('cart.destroy') }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="domain" value="{{ $item['domain'] }}">
                                    <button class="hx-remove" type="submit" data-i18n="remove">Xóa khỏi giỏ hàng</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </section>
                <aside class="hx-card hx-buy-summary" aria-labelledby="hxCartSummaryTitle">
                    <h2 id="hxCartSummaryTitle" data-i18n="summaryTitle">Tổng quan đơn hàng</h2>
                    <p class="hx-subtle" data-i18n="summaryDesc">Kiểm tra lại chi phí trước khi gửi yêu cầu.</p>
                    <div class="hx-total"><span data-i18n="subtotal">Tạm tính</span><strong data-money="{{ $total }}">{{ number_format($total, 0, ',', '.') }}đ</strong></div>
                    <a class="hx-primary hx-primary-block" href="{{ route('checkout.create') }}" data-i18n="proceedCheckout">Tiếp tục đặt hàng</a>
                    @guest
                        <p class="hx-buy-auth-note" data-i18n="loginNeeded">Đăng nhập để đặt hàng; giỏ hàng vẫn được giữ lại.</p>
                    @endguest
                </aside>
            </div>
        @else
            <section class="hx-card hx-buy-empty" aria-labelledby="hxEmptyTitle">
                <span class="hx-buy-empty-icon" aria-hidden="true">⌕</span>
                <h2 id="hxEmptyTitle" data-i18n="emptyTitle">Chưa có tên miền trong giỏ</h2>
                <p data-i18n="emptyCartHelp">Hãy tìm kiếm tên miền bạn muốn và nhấn “Thêm vào giỏ”.</p>
                <a class="hx-primary" href="{{ url('/') }}#domain-search" data-i18n="continueShopping">Tìm tên miền</a>
            </section>
        @endif
    </main>
    @include('partials.hexagon-premium-footer')
</body>
</html>
