@extends('admin.layouts.app')
@section('title', 'Không gian làm việc - HEXAGON')
@section('topbar_title', 'Công việc của tôi')
@section('content')
<div class="page-heading"><div><h1>Không gian làm việc</h1><p>Chào {{ auth('admin')->user()?->name }}. Dưới đây là các chức năng bạn được cấp quyền.</p></div></div>
<section class="panel" style="padding:24px;max-width:800px">
    <p><strong>Vai trò:</strong> {{ \App\Support\HexagonAdminRoles::displayRole(auth('admin')->user()) }}</p>
    @forelse($capabilities as $capability)
        @if(\App\Support\HexagonAdminRoles::can(auth('admin')->user(), $capability))
            <p><a class="text-link" href="{{ match($capability) {
                'orders' => route('admin.orders.index'),
                'pricing' => route('admin.domain-prices.index'),
                'customers' => route('admin.customers.index'),
                'saas' => route('admin.saas.index'),
                'inquiries' => route('admin.saas-inquiries.index'),
            } }}">{{ \App\Support\HexagonAdminRoles::CAPABILITIES[$capability] }}</a></p>
        @endif
    @empty
        <p>Chưa có chức năng được cấp. Liên hệ Super Admin để được phân quyền.</p>
    @endforelse
    <p style="margin-top:18px;color:#64748b;font-size:13px">Các mục không nằm trong quyền được cấp sẽ bị từ chối truy cập, kể cả khi nhập địa chỉ URL trực tiếp.</p>
</section>
@endsection
