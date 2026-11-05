<!doctype html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="theme-color" content="#102e5b">
    <title>Đặt hàng - HEXAGON</title>
    <link rel="stylesheet" href="{{ asset('css/hexagon-shop.css') }}?v=1">
    <script src="{{ asset('js/hexagon-shop-i18n.js') }}?v=1" defer></script>
    <link rel="stylesheet" href="{{ asset('css/hexagon-web-polish.css') }}?v=4">
    <script src="{{ asset('js/hexagon-web-polish.js') }}?v=4" defer></script>
</head>
<body data-page-title="pageCheckout">
    @include('partials.hexagon-shop-header')
    <main class="hx-main hx-container">
        <a class="hx-back" href="{{ route('cart.index') }}" data-i18n="backCart">← Quay lại giỏ hàng</a>
        <div class="hx-intro">
            <h1 data-i18n="checkoutTitle">Thông tin đặt hàng</h1>
            <p data-i18n="checkoutSubtitle">Điền thông tin liên hệ để tạo đơn hàng thử nghiệm.</p>
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
            <section class="hx-card">
                <h2 data-i18n="customerInfo">Thông tin khách hàng</h2>
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
                        <input id="customer_phone" name="customer_phone" type="tel" autocomplete="tel" maxlength="30"
                               value="{{ old('customer_phone') }}"
                               placeholder="Nhập số điện thoại" data-i18n-placeholder="phonePlaceholder" required>
                    </div>
                    <button class="hx-primary hx-primary-block" type="submit" data-i18n="submitOrder">Xác nhận đặt hàng</button>
                </form>
                <p class="hx-subtle hx-checkout-note" data-i18n="checkoutDemo">Hệ thống chỉ tạo đơn demo, không xử lý thanh toán hay mua tên miền thật.</p>
            </section>
            <aside class="hx-card">
                <h2 data-i18n="summaryTitle">Đơn hàng của bạn</h2>
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
