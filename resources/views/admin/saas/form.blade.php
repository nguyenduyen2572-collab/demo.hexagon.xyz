@extends('admin.layouts.app')

@section('title', ($service ? 'Chỉnh sửa' : 'Thêm') . ' dịch vụ SaaS - HEXAGON Admin')
@section('topbar_title', $service ? 'Chỉnh sửa SaaS' : 'Thêm dịch vụ SaaS')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/hexagon-saas.css') }}?v=1">
    <div class="page-heading">
        <h1>{{ $service ? 'Chỉnh sửa dịch vụ' : 'Thêm dịch vụ mới' }}</h1>
        <p>Tiếng Anh có thể để trống; trang tiếng Anh sẽ tự dùng nội dung tiếng Việt.</p>
    </div>
    @if($errors->any())
        <div class="alert alert-error" role="alert">
            <strong>Vui lòng kiểm tra dữ liệu:</strong>
            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif
    <section class="panel saas-admin-form">
        <form action="{{ $service ? route('admin.saas.update', $service->id) : route('admin.saas.store') }}" method="POST">
            @csrf
            @if($service) @method('PATCH') @endif
            <div class="saas-fields">
                <label>Tên dịch vụ (VI) *
                    <input name="name_vi" maxlength="150" required value="{{ old('name_vi', $service?->name_vi ?? '') }}" placeholder="Ví dụ: Phần mềm quản lý ...">
                </label>
                <label>Tên dịch vụ (EN)
                    <input name="name_en" maxlength="150" value="{{ old('name_en', $service?->name_en ?? '') }}">
                </label>
                <label>Slug (URL) *
                    <input name="slug" maxlength="180" required pattern="[a-z0-9]+(-[a-z0-9]+)*" value="{{ old('slug', $service?->slug ?? '') }}" placeholder="vi-du-phan-mem">
                    <small>Chỉ dùng chữ thường a-z, số 0-9 và dấu gạch ngang.</small>
                </label>
                <label>Giá theo tháng (VND)
                    <input type="number" name="price_monthly" min="0" step="1" value="{{ old('price_monthly', $service?->price_monthly ?? '') }}" placeholder="Để trống = Liên hệ">
                </label>
                <label class="saas-wide">Mô tả ngắn (VI)
                    <textarea name="summary_vi" maxlength="500" rows="2">{{ old('summary_vi', $service?->summary_vi ?? '') }}</textarea>
                </label>
                <label class="saas-wide">Mô tả ngắn (EN)
                    <textarea name="summary_en" maxlength="500" rows="2">{{ old('summary_en', $service?->summary_en ?? '') }}</textarea>
                </label>
                <label class="saas-wide">Chi tiết dịch vụ (VI)
                    <textarea name="description_vi" maxlength="10000" rows="6">{{ old('description_vi', $service?->description_vi ?? '') }}</textarea>
                </label>
                <label class="saas-wide">Chi tiết dịch vụ (EN)
                    <textarea name="description_en" maxlength="10000" rows="6">{{ old('description_en', $service?->description_en ?? '') }}</textarea>
                </label>
                <label>Thứ tự hiển thị
                    <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $service?->sort_order ?? 0) }}" required>
                </label>
                <div class="saas-checks">
                    <label><input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" @checked((bool) old('is_active', $service?->is_active ?? false))> Hiển thị công khai</label>
                    <label><input type="hidden" name="is_featured" value="0"><input type="checkbox" name="is_featured" value="1" @checked((bool) old('is_featured', $service?->is_featured ?? false))> Đánh dấu nổi bật</label>
                </div>
            </div>
            <div class="saas-form-actions">
                <button type="submit" class="saas-button">{{ $service ? 'Lưu thay đổi' : 'Thêm dịch vụ' }}</button>
                <a href="{{ route('admin.saas.index') }}" class="saas-button saas-button-outline">Hủy</a>
            </div>
        </form>
    </section>
@endsection
