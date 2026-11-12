<!doctype html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="theme-color" content="#102e5b">
    <title>Dịch vụ SaaS - HEXAGON</title>
    <link rel="stylesheet" href="{{ asset('css/hexagon-shop.css') }}?v=1">
    <link rel="stylesheet" href="{{ asset('css/hexagon-saas.css') }}?v=1">
    <script src="{{ asset('js/hexagon-shop-i18n.js') }}?v=2" defer></script>
    <script src="{{ asset('js/hexagon-saas-i18n.js') }}?v=1" defer></script>
    <link rel="stylesheet" href="{{ asset('css/hexagon-web-polish.css') }}?v=4">
    <script src="{{ asset('js/hexagon-web-polish.js') }}?v=4" defer></script>
    <link rel="stylesheet" href="{{ asset('css/hexagon-unified-ui-v1.css') }}?v=1">
    <link rel="stylesheet" href="{{ asset('css/hexagon-header-cta-v2.css') }}?v=2">
    <script src="{{ asset('js/hexagon-unified-ui-v1.js') }}?v=1" defer></script>
</head>
<body data-saas-title-vi="Dịch vụ SaaS - HEXAGON" data-saas-title-en="SaaS Services - HEXAGON">
    @include('partials.hexagon-shop-header')
    <main class="hx-main hx-container">
        <section class="saas-hero">
            <span class="saas-eyebrow">HEXAGON · SaaS</span>
            <h1 data-saas-vi="Giải pháp phần mềm cho doanh nghiệp" data-saas-en="Software solutions for businesses">Giải pháp phần mềm cho doanh nghiệp</h1>
            <p data-saas-vi="Khám phá danh mục dịch vụ SaaS của HEXAGON. Danh sách và mức giá được quản lý từ hệ thống Admin." data-saas-en="Explore HEXAGON's SaaS offerings. Services and prices are managed in the Admin dashboard.">Khám phá danh mục dịch vụ SaaS của HEXAGON. Danh sách và mức giá được quản lý từ hệ thống Admin.</p>
        </section>
        @if($services->count())
            <section class="saas-grid" aria-label="Danh mục dịch vụ SaaS">
                @foreach($services as $service)
                    <article class="saas-card">
                        <div class="saas-card-top">
                            <span class="saas-chip">SaaS</span>
                            @if($service->is_featured)
                                <span class="saas-chip saas-chip-hot" data-saas-vi="Nổi bật" data-saas-en="Featured">Nổi bật</span>
                            @endif
                        </div>
                        <h2>
                            <a href="{{ route('saas.show', $service->slug) }}"
                                data-saas-vi="{{ $service->name_vi }}"
                                data-saas-en="{{ $service->name_en ?: $service->name_vi }}">{{ $service->name_vi }}</a>
                        </h2>
                        <p class="saas-summary"
                            data-saas-vi="{{ $service->summary_vi ?? '' }}"
                            data-saas-en="{{ $service->summary_en ?: ($service->summary_vi ?? '') }}">{{ $service->summary_vi }}</p>
                        <div class="saas-card-bottom">
                            <div class="saas-price">
                                @if($service->price_monthly === null)
                                    <span data-saas-vi="Liên hệ báo giá" data-saas-en="Contact for pricing">Liên hệ báo giá</span>
                                @elseif($service->price_monthly == 0)
                                    <span data-saas-vi="Miễn phí" data-saas-en="Free">Miễn phí</span>
                                @else
                                    <strong data-saas-currency="{{ $service->price_monthly }}">{{ number_format($service->price_monthly, 0, ',', '.') }}đ</strong>
                                    <small data-saas-vi="/tháng" data-saas-en="/month">/tháng</small>
                                @endif
                            </div>
                            <a class="saas-button" href="{{ route('saas.show', $service->slug) }}"
                                data-saas-vi="Xem chi tiết" data-saas-en="View details">Xem chi tiết</a>
                        </div>
                    </article>
                @endforeach
            </section>
            @if($services->hasPages())
                <nav class="saas-pages" aria-label="Phân trang">
                    @if($services->onFirstPage())
                        <span></span>
                    @else
                        <a href="{{ $services->previousPageUrl() }}" data-saas-vi="Trang trước" data-saas-en="Previous">Trang trước</a>
                    @endif
                    <span>{{ $services->currentPage() }} / {{ $services->lastPage() }}</span>
                    @if($services->hasMorePages())
                        <a href="{{ $services->nextPageUrl() }}" data-saas-vi="Trang sau" data-saas-en="Next">Trang sau</a>
                    @endif
                </nav>
            @endif
        @else
            <div class="saas-empty" role="status">
                <h2 data-saas-vi="Danh mục dịch vụ đang được cập nhật" data-saas-en="Our services are being prepared">Danh mục dịch vụ đang được cập nhật</h2>
                <p data-saas-vi="Vui lòng quay lại sau. Dịch vụ chỉ hiển thị khi Admin kích hoạt." data-saas-en="Please check back later. Services appear once enabled by Admin.">Vui lòng quay lại sau. Dịch vụ chỉ hiển thị khi Admin kích hoạt.</p>
            </div>
        @endif
    </main>
    @include('partials.hexagon-premium-footer')
</body>
</html>
