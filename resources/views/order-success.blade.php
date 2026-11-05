<!doctype html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="theme-color" content="#102e5b">
    <title>Xác nhận đơn hàng - HEXAGON</title>
    <link rel="stylesheet" href="{{ asset('css/hexagon-shop.css') }}?v=1">
    <script src="{{ asset('js/hexagon-shop-i18n.js') }}?v=1" defer></script>
</head>
<body data-page-title="pageSuccess">
    @include('partials.hexagon-shop-header')
    <main class="hx-main hx-container">
        <section class="hx-card hx-success-card">
            <div class="hx-success-icon" aria-hidden="true">✓</div>
            <h1 data-i18n="successTitle">Đặt hàng thành công!</h1>
            <p class="hx-subtle" data-i18n="successDesc">Đơn hàng demo đã được lưu vào hệ thống HEXAGON.</p>
            <p class="hx-order-code"><span data-i18n="orderCode">Mã đơn hàng:</span> <strong>{{ $order->order_code }}</strong></p>
            <div class="hx-order-rows">
                <div class="hx-summary-row">
                    <span data-i18n="status">Trạng thái</span>
                    <strong data-order-status="{{ $order->status }}">
                        @php
                            $statusLabels = [
                                'pending_demo' => 'Chờ xử lý',
                                'processing_demo' => 'Đang xử lý',
                                'completed_demo' => 'Đã xử lý',
                                'cancelled_demo' => 'Đã hủy',
                            ];
                        @endphp
                        {{ $statusLabels[$order->status] ?? $order->status }}
                    </strong>
                </div>
                <h2 style="margin:25px 0 5px;font-size:17px" data-i18n="orderItems">Tên miền đã đặt</h2>
                @foreach($items as $item)
                    <div class="hx-summary-row">
                        <span>{{ $item->domain_name }}</span>
                        <strong data-money="{{ $item->line_total }}">{{ number_format($item->line_total, 0, ',', '.') }}đ</strong>
                    </div>
                @endforeach
                <div class="hx-total">
                    <strong data-i18n="total">Tổng giá trị đơn hàng</strong>
                    <strong data-money="{{ $order->total_amount }}">{{ number_format($order->total_amount, 0, ',', '.') }}đ</strong>
                </div>
            </div>
            <p class="hx-subtle hx-checkout-note" data-i18n="checkoutDemo">Hệ thống chỉ tạo đơn demo, không xử lý thanh toán hay mua tên miền thật.</p>
            
        </section>
    </main>
    <footer class="hx-footer"><span data-i18n="demoNote">HEXAGON · Môi trường thử nghiệm Local — Chưa thanh toán hoặc đăng ký tên miền thật.</span></footer>
</body>
</html>
