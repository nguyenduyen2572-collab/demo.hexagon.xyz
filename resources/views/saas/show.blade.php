<!doctype html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="theme-color" content="#102e5b">
    <title>{{ $service->name_vi }} - HEXAGON</title>
    <link rel="stylesheet" href="{{ asset('css/hexagon-shop.css') }}?v=1">
    <link rel="stylesheet" href="{{ asset('css/hexagon-saas.css') }}?v=1">
    <script src="{{ asset('js/hexagon-shop-i18n.js') }}?v=2" defer></script>
    <script src="{{ asset('js/hexagon-saas-i18n.js') }}?v=1" defer></script>
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
    <link rel="stylesheet" href="{{ asset('css/hexagon-saas-inquiries-v1.css') }}?v=1">
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
                <p class="saas-detail-note" data-saas-vi="Liên hệ HEXAGON để được tư vấn và xác nhận giải pháp phù hợp." data-saas-en="Contact HEXAGON for consultation and a suitable solution.">Liên hệ HEXAGON để được tư vấn và xác nhận giải pháp phù hợp.</p>
            </aside>
        </article>
        @include('partials.saas-inquiry-form')
    </main>
    @include('partials.hexagon-premium-footer')
</body>
</html>
