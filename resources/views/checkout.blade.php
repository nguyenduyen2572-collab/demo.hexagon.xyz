<!doctype html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="theme-color" content="#102e5b">
    <title>Đặt hàng - HEXAGON</title>
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
<body class="hx-buy-v6 hx-buy-checkout" data-page-title="pageCheckout">
    @include('partials.hexagon-shop-header')
    <main class="hx-main hx-container" id="mainContent">
        <nav class="hx-buy-progress" aria-label="Các bước đặt hàng" data-i18n-aria="orderStepsAria">
            <span class="is-done"><span class="hx-progress-num">✓</span><span data-i18n="stepFind">Tìm tên miền</span></span>
            <a class="is-done" href="{{ route('cart.index') }}"><span class="hx-progress-num">✓</span><span data-i18n="stepCart">Giỏ hàng</span></a>
            <span class="is-active" aria-current="step"><span class="hx-progress-num">3</span><span data-i18n="stepCheckout">Đặt hàng</span></span>
        </nav>
        <a class="hx-back" href="{{ route('cart.index') }}" data-i18n="backCart">← Quay lại giỏ hàng</a>
        <div class="hx-intro hx-buy-intro">
            <div><span class="hx-buy-eyebrow" data-i18n="checkoutEyebrow">BƯỚC 3 / 3</span>
            <h1 data-i18n="checkoutTitle">Thông tin đặt hàng</h1>
            <p data-i18n="checkoutSubtitle">Điền thông tin liên hệ để lưu cùng đơn hàng của bạn.</p></div>
        </div>
        @if(session('error'))
            <div class="hx-alert" role="alert">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="hx-alert" role="alert">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif
        <div class="hx-checkout-grid">
            <section class="hx-card hx-buy-form-card">
                <h2 data-i18n="customerInfo">Thông tin liên hệ</h2>
                <p class="hx-subtle hx-buy-form-hint" data-i18n="contactHint">Những trường có dấu * là bắt buộc.</p>
                <form method="POST" action="{{ route('checkout.store') }}">
                    @csrf
                    <div class="hx-field">
                        <label for="customer_name" data-i18n="fullName">Họ và tên *</label>
                        <input id="customer_name" name="customer_name" type="text" autocomplete="name" maxlength="150"
                               value="{{ old('customer_name', auth()->check() && !auth()->user()->is_admin ? auth()->user()->name : '') }}"
                               placeholder="Nhập họ và tên" data-i18n-placeholder="namePlaceholder" required>
                    </div>
                    <div class="hx-field">
                        <label for="customer_email" data-i18n="emailLabel">Email *</label>
                        <input id="customer_email" name="customer_email" type="email" autocomplete="email" maxlength="190"
                               value="{{ old('customer_email', auth()->check() && !auth()->user()->is_admin ? auth()->user()->email : '') }}"
                               placeholder="Nhập email" data-i18n-placeholder="emailPlaceholder" required>
                    </div>
                    <div class="hx-field">
                        <label for="customer_phone" data-i18n="phoneLabel">Số điện thoại *</label>
                        <input id="customer_phone" name="customer_phone" type="tel" inputmode="tel" autocomplete="tel" maxlength="30"
                               value="{{ old('customer_phone') }}"
                               placeholder="Nhập số điện thoại" data-i18n-placeholder="phonePlaceholder" required>
                    </div>
                    <button class="hx-primary hx-primary-block" type="submit" data-i18n="submitOrder">Gửi yêu cầu đặt hàng</button>
                </form>
                <p class="hx-subtle hx-checkout-note" data-i18n="checkoutDemo">Hệ thống chỉ tạo đơn demo, không xử lý thanh toán hay mua tên miền thật.</p>
            </section>
            <aside class="hx-card hx-buy-summary" aria-labelledby="hxCheckoutSummaryTitle">
                <h2 id="hxCheckoutSummaryTitle" data-i18n="summaryTitle">Tóm tắt đơn hàng</h2>
                @foreach($items as $item)
                    <div class="hx-summary-row">
                        <div>
                            <div class="hx-domain">{{ $item['domain'] }}</div>
                            <div class="hx-meta" data-i18n="itemOneYear">Đăng ký 1 năm</div>
                        </div>
                        <strong class="hx-money" data-money="{{ $item['registration_price'] }}">{{ number_format($item['registration_price'], 0, ',', '.') }}đ</strong>
                    </div>
                @endforeach
                <div class="hx-total">
                    <strong data-i18n="subtotal">Tạm tính</strong>
                    <strong data-money="{{ $total }}">{{ number_format($total, 0, ',', '.') }}đ</strong>
                </div>
                <p class="hx-subtle" style="margin:18px 0 0" data-i18n="amountMayChange">Giá được Backend kiểm tra lại tại thời điểm đặt hàng.</p>
            </aside>
        </div>
    </main>
    @include('partials.hexagon-premium-footer')
</body>
</html>
