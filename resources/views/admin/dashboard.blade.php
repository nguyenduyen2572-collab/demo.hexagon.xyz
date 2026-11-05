@extends('admin.layouts.app')

@section('title', 'Dashboard - HEXAGON Admin')
@section('topbar_title', 'Dashboard quản trị')

@section('content')
    <div class="page-heading">
        <h1>Tổng quan hệ thống</h1>
        <p>Thống kê hoạt động của HEXAGON trên Local.</p>
    </div>

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
            <div class="stat-label">Tổng giá trị đơn demo</div>
            <div class="stat-value">{{ number_format($stats['total_demo_amount'], 0, ',', '.') }}đ</div>
            <div class="stat-note">Không phải doanh thu đã thanh toán</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Khách đặt hàng</div>
            <div class="stat-value">{{ number_format($stats['unique_customers']) }}</div>
            <div class="stat-note">Số email khách hàng khác nhau</div>
        </div>
    </section>

    <section class="panel">
        <div class="panel-heading">
            <h2>Đơn hàng mới nhất</h2>
            <a class="text-link" href="{{ route('admin.orders.index') }}">Xem tất cả</a>
        </div>
        <div class="table-wrap">
            <table class="admin-table">
                <thead><tr>
                    <th>ID</th><th>Mã đơn</th><th>Khách hàng</th><th>Email</th>
                    <th>Tổng tiền</th><th>Trạng thái</th><th>Ngày tạo</th>
                </tr></thead>
                <tbody>
                @forelse ($recentOrders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td class="code-cell"><a class="text-link" href="{{ route('admin.orders.show', ['id' => $order->id]) }}">{{ $order->order_code }}</a></td>
                        <td>{{ $order->customer_name }}</td>
                        <td>{{ $order->customer_email }}</td>
                        <td><strong>{{ number_format($order->total_amount, 0, ',', '.') }}đ</strong></td>
                        <td><x-admin.status :status="$order->status" /></td>
                        <td>{{ $order->created_at }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty-cell">Chưa có đơn hàng nào.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
    