@extends('admin.layouts.app')

@section('title', 'Tổng quan - HEXAGON Admin')
@section('topbar_title', 'Tổng quan')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/hexagon-admin-dashboard-v3.css') }}?v=2">
@endpush
@section('content')
    {{-- HEXAGON DASHBOARD V3: BEGIN --}}
    <section class="hx-dash-v3-totals" aria-labelledby="hx-dash-v3-totals-title">
        <header class="hx-dash-v3-overview-head">
            <div>
                <span class="hx-dash-v3-eyebrow">TOÀN BỘ THỜI GIAN</span>
                <h2 id="hx-dash-v3-totals-title">Chỉ số tổng hợp</h2>
            </div>
        </header>
    <section class="stats-grid" aria-label="Thống kê đơn hàng">
        <div class="stat-card">
            <div class="stat-label">Tổng đơn hàng</div>
            <div class="stat-value">{{ number_format($stats['total_orders']) }}</div>
            <div class="stat-note">Tất cả đơn đã tạo</div>
            <a class="hx-dash-v3-stat-link" href="{{ route('admin.orders.index') }}">Xem tất cả đơn <span aria-hidden="true">→</span></a>
        </div>
        <div class="stat-card">
            <div class="stat-label">Đơn chờ xử lý</div>
            <div class="stat-value">{{ number_format($stats['pending_orders']) }}</div>
            <div class="stat-note">Trạng thái chờ xử lý</div>
            <a class="hx-dash-v3-stat-link" href="{{ route('admin.orders.index', ['status' => 'pending_demo']) }}">Xem đơn chờ xử lý <span aria-hidden="true">→</span></a>
        </div>
        <div class="stat-card">
            <div class="stat-label">Đơn đã hoàn tất</div>
            <div class="stat-value">{{ number_format($stats['completed_orders']) }}</div>
            <div class="stat-note">Đã hoàn thành xử lý</div>
            <a class="hx-dash-v3-stat-link" href="{{ route('admin.orders.index', ['status' => 'completed_demo']) }}">Xem đơn hoàn tất <span aria-hidden="true">→</span></a>
        </div>
        <div class="stat-card">
            <div class="stat-label">Khách đặt hàng</div>
            <div class="stat-value">{{ number_format($stats['unique_customers']) }}</div>
            <div class="stat-note">Email đặt hàng không trùng lặp</div>
            <a class="hx-dash-v3-stat-link" href="{{ route('admin.customers.index') }}">Xem danh sách khách <span aria-hidden="true">→</span></a>
        </div>
    </section>

    </section>

    <x-admin.dashboard-analytics />

    <h2 class="hx-dash-v3-tools-title">Thao tác nhanh</h2>
    <nav class="hx-dashboard-tools" aria-label="Truy cập nhanh">
        <a href="{{ route('admin.orders.index') }}"><svg class="hx-icon"><use href="#hx-ico-orders"/></svg> Đơn hàng</a>
        <a href="{{ route('admin.customers.index') }}"><svg class="hx-icon"><use href="#hx-ico-users"/></svg> Khách hàng</a>
        <a href="{{ route('admin.domain-prices.index') }}"><svg class="hx-icon"><use href="#hx-ico-domain"/></svg> Tên miền</a>
        <a href="{{ route('admin.saas.index') }}"><svg class="hx-icon"><use href="#hx-ico-saas"/></svg> Dịch vụ SaaS</a>
    </nav>
    <section class="panel hx-dash-v3-recent">
        <div class="panel-heading">
            <h2>Đơn hàng mới nhất</h2>
            <a class="text-link" href="{{ route('admin.orders.index') }}">Xem tất cả</a>
        </div>
        <div class="table-wrap">
            <table class="admin-table">
                <thead><tr>
                    <th>Đơn hàng</th><th>Khách hàng</th><th>Email</th>
                    <th>Tổng tiền</th><th>Trạng thái</th><th>Ngày tạo</th>
                </tr></thead>
                <tbody>
                @forelse ($recentOrders as $order)
                    <tr>
                        <td class="code-cell"><a class="text-link" href="{{ route('admin.orders.show', ['id' => $order->id]) }}">{{ \App\Support\HexagonOrderCode::forOrder($order) }}</a></td>
                        <td>{{ $order->customer_name }}</td>
                        <td>{{ $order->customer_email }}</td>
                        <td><strong>{{ number_format($order->total_amount, 0, ',', '.') }}đ</strong></td>
                        <td><x-admin.status :status="$order->status" /></td>
                        <td>{{ \Illuminate\Support\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty-cell">Chưa có đơn hàng nào.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>


    {{-- HEXAGON DASHBOARD V3: END --}}
@endsection
