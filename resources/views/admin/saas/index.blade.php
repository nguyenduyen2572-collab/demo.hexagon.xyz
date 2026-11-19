@extends('admin.layouts.app')

@section('title', 'Dịch vụ SaaS - HEXAGON Admin')
@section('topbar_title', 'Quản lý dịch vụ SaaS')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/hexagon-saas.css') }}?v=3">
    <div class="page-heading saas-admin-head">
        <div>
            <h1>Danh mục dịch vụ SaaS</h1>
            <p>Thêm, chỉnh sửa và quản lý các dịch vụ đang kinh doanh.</p>
        </div>
        <a class="saas-button" href="{{ route('admin.saas.create') }}">+ Thêm dịch vụ</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success" role="status">{{ session('success') }}</div>
    @endif
    @if(session('warning'))
        <div class="alert saas-v83-warning" role="status">{{ session('warning') }}</div>
    @endif

    <form class="saas-admin-filters" action="{{ route('admin.saas.index') }}" method="GET" role="search">
        <label class="saas-admin-filter-field" for="saas-filter-search">
            <span>Tìm dịch vụ</span>
            <input id="saas-filter-search" type="search" name="q" value="{{ $search }}" maxlength="100" placeholder="Tên dịch vụ hoặc đường dẫn..." autocomplete="off">
        </label>
        <label class="saas-admin-filter-field saas-admin-filter-status" for="saas-filter-status">
            <span>Trạng thái</span>
            <select id="saas-filter-status" name="status">
                <option value="">Tất cả trạng thái</option>
                <option value="active" @selected($status === 'active')>Đang hiển thị</option>
                <option value="hidden" @selected($status === 'hidden')>Đang ẩn</option>
            </select>
        </label>
        <button type="submit" class="saas-admin-filter-submit">Tìm kiếm</button>
        @if($search !== '' || $status !== '')
            <a class="saas-admin-filter-reset" href="{{ route('admin.saas.index') }}">Xóa lọc</a>
        @endif
    </form>
    <p class="saas-admin-result-count" role="status">Tìm thấy <strong>{{ number_format($services->total(), 0, ',', '.') }}</strong> dịch vụ{{ ($search !== '' || $status !== '') ? ' phù hợp' : '' }}.</p>

    <section class="panel">
        <div class="table-wrap">
            <table class="admin-table saas-admin-table">
                <thead>
                    <tr><th>Tên dịch vụ</th><th>Đường dẫn</th><th>Giá/tháng</th><th>Trạng thái</th><th>Thao tác</th></tr>
                </thead>
                <tbody>
                    @forelse($services as $service)
                        <tr>
                            <td><strong>{{ $service->name_vi }}</strong>
                                @if($service->is_featured)<span class="saas-admin-tag">Nổi bật</span>@endif
                            </td>
                            <td><code>{{ $service->slug }}</code></td>
                            <td>
                                @if($service->price_monthly === null) Liên hệ
                                @else {{ number_format($service->price_monthly, 0, ',', '.') }}đ
                                @endif
                            </td>
                            <td><span class="saas-admin-status {{ $service->is_active ? 'is-on' : 'is-off' }}">{{ $service->is_active ? 'Đang hiển thị' : 'Đang ẩn' }}</span></td>
                            <td>
                                <div class="saas-admin-actions">
                                    <a class="saas-admin-action-button is-edit" href="{{ route('admin.saas.edit', array_merge(['id' => $service->id], request()->only(['q', 'status', 'page']))) }}">Chỉnh sửa</a>
                                    <form method="POST" action="{{ route('admin.saas.toggle', array_merge(['id' => $service->id], request()->only(['q', 'status', 'page']))) }}"
                                        @if($service->is_active) onsubmit="return confirm('Bạn muốn ẩn dịch vụ này khỏi website? Có thể hiển thị lại sau.')" @endif>
                                        @csrf @method('PATCH')
                                        <button class="saas-admin-action-button {{ $service->is_active ? 'is-hide' : 'is-show' }}" type="submit">{{ $service->is_active ? 'Ẩn' : 'Hiển thị' }}</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td class="empty-cell" colspan="5">{{ ($search !== '' || $status !== '') ? 'Không tìm thấy dịch vụ phù hợp. Hãy thử từ khóa hoặc bộ lọc khác.' : 'Chưa có dịch vụ nào. Hãy chọn “Thêm dịch vụ”.' }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($services->hasPages())
            <div class="pagination-bar">
                <div>@if($services->previousPageUrl()) <a class="text-link" href="{{ $services->previousPageUrl() }}">Trang trước</a> @endif</div>
                <span>Trang {{ $services->currentPage() }} / {{ $services->lastPage() }}</span>
                <div>@if($services->nextPageUrl()) <a class="text-link" href="{{ $services->nextPageUrl() }}">Trang sau</a> @endif</div>
            </div>
        @endif
    </section>
@endsection
