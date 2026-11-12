@extends('admin.layouts.app')

@section('title', 'Tổng quan - HEXAGON Admin')
@section('topbar_title', 'Tổng quan')

@section('content')
    <section class="hx-dashboard-hero" aria-labelledby="hx-admin-home-title">
        <div>
            <span class="hx-hero-eyebrow">TRUNG TÂM ĐIỀU HÀNH</span>
            <h1 id="hx-admin-home-title">Chào mừng trở lại!</h1>
            <p>Theo dõi đơn hàng và quản lý hoạt động kinh doanh ngay tại đây.</p>
        </div>
        <div class="hx-hero-actions">
            <a class="hx-hero-primary" href="{{ route('admin.orders.index') }}"><svg class="hx-icon"><use href="#hx-ico-orders"/></svg> Xử lý đơn hàng</a>
            <a class="hx-hero-secondary" href="{{ route('admin.domain-prices.index') }}">Quản lý bảng giá <svg class="hx-icon"><use href="#hx-ico-arrow"/></svg></a>
        </div>
    </section>

    <section class="stats-grid" aria-label="Thống kê đơn hàng">
        <div class="stat-card">
            <div class="stat-label">Tổng đơn hàng</div>
            <div class="stat-value">{{ number_format($stats['total_orders']) }}</div>
            <div class="stat-note">Tất cả đơn đã tạo</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Đơn chờ xử lý</div>
            <div class="stat-value">{{ number_format($stats['pending_orders']) }}</div>
            <div class="stat-note">Trạng thái chờ xử lý</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Giá trị đơn hàng</div>
            <div class="stat-value">{{ number_format($stats['total_demo_amount'], 0, ',', '.') }}đ</div>
            <div class="stat-note">Tổng giá trị đơn đã tạo</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Khách đặt hàng</div>
            <div class="stat-value">{{ number_format($stats['unique_customers']) }}</div>
            <div class="stat-note">Email đặt hàng không trùng lặp</div>
        </div>
    </section>

    <nav class="hx-dashboard-tools" aria-label="Truy cập nhanh">
        <a href="{{ route('admin.orders.index') }}"><svg class="hx-icon"><use href="#hx-ico-orders"/></svg> Đơn hàng</a>
        <a href="{{ route('admin.customers.index') }}"><svg class="hx-icon"><use href="#hx-ico-users"/></svg> Khách hàng</a>
        <a href="{{ route('admin.domain-prices.index') }}"><svg class="hx-icon"><use href="#hx-ico-domain"/></svg> Tên miền</a>
        <a href="{{ route('admin.saas.index') }}"><svg class="hx-icon"><use href="#hx-ico-saas"/></svg> Dịch vụ SaaS</a>
    </nav>
    <section class="panel">
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
                        <td class="code-cell"><a class="text-link" href="{{ route('admin.orders.show', ['id' => $order->id]) }}">#{{ $order->id }}</a><br><span class="muted">{{ $order->order_code }}</span></td>
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

{{-- HEXAGON ANALYTICS V2.1: BEGIN --}}
<x-admin.dashboard-analytics />
{{-- HEXAGON ANALYTICS V2.1: END --}}
@endsection
    