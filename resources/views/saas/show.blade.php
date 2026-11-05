<!doctype html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="theme-color" content="#102e5b">
    <title>{{ $service->name_vi }} - HEXAGON</title>
    <link rel="stylesheet" href="{{ asset('css/hexagon-shop.css') }}?v=1">
    <link rel="stylesheet" href="{{ asset('css/hexagon-saas.css') }}?v=1">
    <script src="{{ asset('js/hexagon-shop-i18n.js') }}?v=1" defer></script>
    <script src="{{ asset('js/hexagon-saas-i18n.js') }}?v=1" defer></script>
</head>
<body data-saas-title-vi="{{ $service->name_vi }} - HEXAGON" data-saas-title-en="{{ $service->name_en ?: $service->name_vi }} - HEXAGON">
    @include('partials.hexagon-shop-header')
    <main class="hx-main hx-container">
        <article class="saas-detail">
            <div>
                <span class="saas-eyebrow">HEXAGON · SaaS</span>
                <h1 data-saas-vi="{{ $service->name_vi }}" data-saas-en="{{ $service->name_en ?: $service->name_vi }}">{{ $service->name_vi }}</h1>
                <p class="saas-detail-lead"
                    data-saas-vi="{{ $service->summary_vi ?? '' }}"
                    data-saas-en="{{ $service->summary_en ?: ($service->summary_vi ?? '') }}">{{ $service->summary_vi }}</p>
                @if($service->description_vi || $service->description_en)
                    <div class="saas-detail-description"
                        data-saas-vi="{{ $service->description_vi ?: ($service->description_en ?? '') }}"
                        data-saas-en="{{ $service->description_en ?: ($service->description_vi ?? '') }}">{{ $service->description_vi ?: $service->description_en }}</div>
                @endif
            </div>
            <aside class="saas-detail-side">
                <div class="saas-chip">SaaS</div>
                <p data-saas-vi="Giá dịch vụ" data-saas-en="Service pricing">Giá dịch vụ</p>
                <div class="saas-detail-price">
                    @if($service->price_monthly === null)
                        <strong data-saas-vi="Liên hệ báo giá" data-saas-en="Contact for pricing">Liên hệ báo giá</strong>
                    @elseif($service->price_monthly == 0)
                        <strong data-saas-vi="Miễn phí" data-saas-en="Free">Miễn phí</strong>
                    @else
                        <strong data-saas-currency="{{ $service->price_monthly }}">{{ number_format($service->price_monthly, 0, ',', '.') }}đ</strong>
                        <small data-saas-vi="/tháng" data-saas-en="/month">/tháng</small>
                    @endif
                </div>
                <p class="saas-detail-note" data-saas-vi="Đây là danh mục giới thiệu. Chưa kích hoạt đăng ký hoặc thanh toán dịch vụ SaaS." data-saas-en="This is a service catalog. SaaS subscriptions and payments are not enabled yet.">Đây là danh mục giới thiệu. Chưa kích hoạt đăng ký hoặc thanh toán dịch vụ SaaS.</p>
            </aside>
        </article>
    </main>
    <footer class="hx-footer">HEXAGON · SaaS</footer>
</body>
</html>
