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
</head>
<body data-page-title="pageCart">
    @include('partials.hexagon-shop-header')
    <main class="hx-main hx-container">
        
        <div class="hx-intro">
            <h1 data-i18n="cartTitle">Giỏ hàng của bạn</h1>
            <p data-i18n="cartSubtitle">Kiểm tra tên miền trước khi đặt hàng thử nghiệm.</p>
        </div>
        @if(session('error'))
            <div class="hx-alert" role="alert">{{ session('error') }}</div>
        @endif
        <section class="hx-card">
            @forelse($items as $item)
                <div class="hx-cart-item">
                    <div>
                        <div class="hx-domain">{{ $item['domain'] }}</div>
                        <div class="hx-meta" data-i18n="itemOneYear">Đăng ký 1 năm</div>
                    </div>
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
            @empty
                <p class="hx-empty" data-i18n="emptyCart">Giỏ hàng của bạn đang trống.</p>
            @endforelse
            @if(count($items) > 0)
                <div class="hx-total">
                    <strong data-i18n="subtotal">Tạm tính</strong>
                    <strong data-money="{{ $total }}">{{ number_format($total, 0, ',', '.') }}đ</strong>
                </div>
                <a class="hx-primary hx-primary-block" href="{{ route('checkout.create') }}" data-i18n="proceedCheckout">Tiếp tục đặt hàng</a>
            @else
                <a class="hx-primary hx-primary-block" href="{{ url('/') }}" data-i18n="continueShopping">Tiếp tục tìm tên miền</a>
            @endif
        </section>
        <p class="hx-subtle" style="margin:18px 0" data-i18n="cartDemo">Giỏ hàng demo. Không thanh toán hay đăng ký tên miền thật.</p>
    </main>
    @include('partials.hexagon-premium-footer')
</body>
</html>
