@extends('admin.layouts.app')

@section('title', 'Dịch vụ SaaS - HEXAGON Admin')
@section('topbar_title', 'Quản lý dịch vụ SaaS')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/hexagon-saas.css') }}?v=1">
    <div class="page-heading saas-admin-head">
        <div>
            <h1>Danh mục dịch vụ SaaS</h1>
            <p>Thêm, chỉnh sửa và bật/tắt dịch vụ. Không ảnh hưởng hệ thống tên miền hiện tại.</p>
        </div>
        <a class="saas-button" href="{{ route('admin.saas.create') }}">+ Thêm dịch vụ</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success" role="status">{{ session('success') }}</div>
    @endif

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
                                    <a class="text-link" href="{{ route('admin.saas.edit', $service->id) }}">Chỉnh sửa</a>
                                    <form method="POST" action="{{ route('admin.saas.toggle', $service->id) }}">
                                        @csrf @method('PATCH')
                                        <button class="saas-admin-toggle" type="submit">{{ $service->is_active ? 'Ẩn' : 'Hiển thị' }}</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td class="empty-cell" colspan="5">Chưa có dịch vụ nào. Hãy chọn “Thêm dịch vụ”.</td></tr>
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
