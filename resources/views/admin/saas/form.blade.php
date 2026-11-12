@extends('admin.layouts.app')

@section('title', ($service ? 'Chỉnh sửa' : 'Thêm') . ' dịch vụ SaaS - HEXAGON Admin')
@section('topbar_title', $service ? 'Chỉnh sửa dịch vụ SaaS' : 'Thêm dịch vụ SaaS')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/hexagon-saas.css') }}?v=1">
    <link rel="stylesheet" href="{{ asset('css/hexagon-saas-admin-v2.css') }}?v=2">
    <script src="{{ asset('js/hexagon-saas-admin-v2.js') }}?v=3" defer></script>

    <div class="page-heading saas-v2-heading">
        <div>
            <span class="saas-v2-kicker">DỊCH VỤ / QUẢN LÝ NỘI DUNG</span>
            <h1>{{ $service ? 'Chỉnh sửa dịch vụ' : 'Thêm dịch vụ mới' }}</h1>
            <p>Chỉ cần nhập thông tin tiếng Việt. Hệ thống tự tạo đường dẫn và bản dịch tiếng Anh.</p>
        </div>
        <a class="saas-v2-back" href="{{ route('admin.saas.index') }}">← Danh sách dịch vụ</a>
    </div>

    @if($errors->any())
        <div class="alert alert-error saas-v2-error" role="alert">
            <strong>Vui lòng kiểm tra dữ liệu:</strong>
            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <section class="panel saas-admin-form saas-v2-panel">
        <form id="saas-admin-editor" action="{{ $service ? route('admin.saas.update', $service->id) : route('admin.saas.store') }}"
            data-translate-url="{{ route('admin.saas.translate-preview') }}" method="POST">
            @csrf
            @if($service) @method('PATCH') @endif

            <div class="saas-v2-section-heading">
                <div class="saas-v2-section-icon" aria-hidden="true">01</div>
                <div><h2>Thông tin dịch vụ</h2><p>Tên gọi, đường dẫn truy cập và mức phí.</p></div>
            </div>
            <div class="saas-fields saas-v2-fields">
                <label for="saas-name-vi">Tên dịch vụ <span class="saas-v2-required">*</span>
                    <input id="saas-name-vi" name="name_vi" maxlength="150" required
                        value="{{ old('name_vi', $service?->name_vi ?? '') }}" placeholder="Ví dụ: Phần mềm quản lý bán hàng" autocomplete="off">
                </label>
                <label for="saas-slug">Đường dẫn (slug) <span class="saas-v2-required">*</span>
                    <input id="saas-slug" name="slug" maxlength="180" required pattern="[a-z0-9]+(-[a-z0-9]+)*"
                        value="{{ old('slug', $service?->slug ?? '') }}" placeholder="Tự tạo từ tên dịch vụ" autocapitalize="off" spellcheck="false">
                    <small>Được tạo tự động. Bạn vẫn có thể sửa đường dẫn nếu cần.</small>
                </label>
                <label for="saas-price">Giá theo tháng (VND)
                    <div class="saas-v2-currency-wrap">
                        <input id="saas-price" name="price_monthly" type="text" inputmode="numeric" autocomplete="off"
                            value="{{ old('price_monthly', $service?->price_monthly ?? '') }}" placeholder="199.000" aria-describedby="saas-price-help saas-price-error">
                        <span class="saas-v2-currency-unit" aria-hidden="true">₫ / tháng</span>
                    </div>
                    <small id="saas-price-help">Nhập 199000 hoặc 199.000. Khi rời ô, hệ thống định dạng thành 199.000đ. Để trống nếu cần báo giá.</small>
                    <small id="saas-price-error" class="saas-v2-input-error" role="status" hidden></small>
                </label>
                <label for="saas-sort">Thứ tự hiển thị
                    <input id="saas-sort" type="number" name="sort_order" min="0" max="1000000" step="1"
                        value="{{ old('sort_order', $service?->sort_order ?? 0) }}" required>
                    <small>Số nhỏ hơn được ưu tiên hiển thị trước.</small>
                </label>
            </div>

            <div class="saas-v2-section-heading saas-v2-divider-top">
                <div class="saas-v2-section-icon" aria-hidden="true">02</div>
                <div><h2>Nội dung giới thiệu</h2><p>Viết bằng tiếng Việt; bản tiếng Anh được tạo tự động.</p></div>
            </div>
            <div class="saas-fields saas-v2-fields">
                <label class="saas-wide" for="saas-summary-vi">Mô tả ngắn
                    <textarea id="saas-summary-vi" name="summary_vi" maxlength="500" rows="3" placeholder="Nêu lợi ích chính của dịch vụ trong 1–2 câu">{{ old('summary_vi', $service?->summary_vi ?? '') }}</textarea>
                </label>
                <label class="saas-wide" for="saas-description-vi">Giới thiệu chi tiết
                    <textarea id="saas-description-vi" name="description_vi" maxlength="10000" rows="7" placeholder="Các tính năng, lợi ích và đối tượng khách hàng phù hợp">{{ old('description_vi', $service?->description_vi ?? '') }}</textarea>
                </label>
            </div>

            <details class="saas-v2-translation" id="saas-english-preview">
                <summary>
                    <span><strong>Xem bản dịch tiếng Anh</strong><small>Được tự động cập nhật sau khi nhập nội dung tiếng Việt</small></span>
                    <span class="saas-v2-summary-chevron" aria-hidden="true">⌄</span>
                </summary>
                <div class="saas-v2-translation-body">
                    <div class="saas-v2-translation-row" data-translation-row="name_vi"><strong>Tên dịch vụ (EN)</strong><p data-translation-result="name_vi">{{ old('name_en', $service?->name_en ?? 'Chưa có nội dung') }}</p></div>
                    <div class="saas-v2-translation-row" data-translation-row="summary_vi"><strong>Mô tả ngắn (EN)</strong><p data-translation-result="summary_vi">{{ old('summary_en', $service?->summary_en ?? 'Chưa có nội dung') }}</p></div>
                    <div class="saas-v2-translation-row" data-translation-row="description_vi"><strong>Chi tiết dịch vụ (EN)</strong><p data-translation-result="description_vi">{{ old('description_en', $service?->description_en ?? 'Chưa có nội dung') }}</p></div>
                    <p class="saas-v2-translation-note" id="saas-translation-status" role="status" aria-live="polite">Nội dung tiếng Anh sẽ được đồng bộ khi lưu.</p>
                </div>
            </details>

            <div class="saas-v2-section-heading saas-v2-divider-top">
                <div class="saas-v2-section-icon" aria-hidden="true">03</div>
                <div><h2>Hiển thị dịch vụ</h2><p>Quyết định cách dịch vụ xuất hiện trên website.</p></div>
            </div>
            <div class="saas-v2-switches">
                <label class="saas-v2-switch-row">
                    <span><strong>Hiển thị công khai</strong><small>Khách hàng có thể tìm thấy dịch vụ trên website</small></span>
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" @checked((bool) old('is_active', $service?->is_active ?? false))>
                </label>
                <label class="saas-v2-switch-row">
                    <span><strong>Đánh dấu nổi bật</strong><small>Ưu tiên hiển thị trên danh sách dịch vụ</small></span>
                    <input type="hidden" name="is_featured" value="0">
                    <input type="checkbox" name="is_featured" value="1" @checked((bool) old('is_featured', $service?->is_featured ?? false))>
                </label>
            </div>
            <div class="saas-form-actions saas-v2-actions">
                <button type="submit" class="saas-v2-primary">{{ $service ? 'Lưu thay đổi' : 'Thêm dịch vụ' }}</button>
                <a href="{{ route('admin.saas.index') }}" class="saas-v2-cancel">Hủy</a>
            </div>
        </form>
    </section>
@endsection
