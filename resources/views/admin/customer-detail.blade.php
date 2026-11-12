
@extends('admin.layouts.app')

@section('title', 'Chi tiết khách hàng - HEXAGON')
@section('topbar_title', 'Chi tiết khách hàng')

@section('content')

<a class="back-button"
   href="{{ route('admin.customers.index') }}">
    ← Quay lại danh sách
</a>

<div class="page-heading">
    <h1>Thông tin khách hàng</h1>
    <p>Thông tin và lịch sử sử dụng dịch vụ HEXAGON.</p>
</div>

<!-- THÔNG TIN KHÁCH HÀNG -->
<section class="panel customer-detail-panel">
    <div class="customer-detail-heading">
        <div class="customer-avatar">
            {{ mb_strtoupper(mb_substr($customer->customer_name, 0, 1)) }}
        </div>

        <div>
            <h2>{{ $customer->customer_name }}</h2>
            <p>{{ $customer->customer_email }}</p>
        </div>
    </div>

    <div class="customer-info-grid">
        <div>
            <span>Họ tên</span>
            <strong>{{ $customer->customer_name }}</strong>
        </div>

        <div>
            <span>Email</span>
            <strong>{{ $customer->customer_email }}</strong>
        </div>

        <div>
            <span>Số điện thoại</span>
            <strong>{{ $customer->customer_phone }}</strong>
        </div>

        <div>
            <span>Đơn hàng gần nhất</span>
            <strong>{{ $stats->last_order_at }}</strong>
        </div>
    </div>
</section>

<!-- THỐNG KÊ -->
<div class="customer-stats">
    <div class="customer-stat-card">
        <span>Tổng đơn hàng</span>
        <strong>{{ number_format($stats->order_count) }}</strong>
    </div>

    <div class="customer-stat-card">
        <span>Tổng giá trị đặt hàng</span>
        <strong>
            {{ number_format($stats->total_value, 0, ',', '.') }}đ
        </strong>
    </div>
</div>

<!-- LỊCH SỬ ĐƠN HÀNG -->
<section class="panel">
    <div class="customer-section-heading">
        <h2>Lịch sử đặt hàng</h2>
    </div>

    @php
        $statusLabels = [
            'pending_demo' => 'Chờ xử lý',
            'processing_demo' => 'Đang xử lý',
            'completed_demo' => 'Đã xử lý',
            'cancelled_demo' => 'Đã hủy',
        ];

        $statusClasses = [
            'pending_demo' => 'customer-pending',
            'processing_demo' => 'customer-processing',
            'completed_demo' => 'customer-completed',
            'cancelled_demo' => 'customer-cancelled',
        ];
    @endphp

    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Mã đơn</th>
                    <th>Giá trị</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                    <th>Thao tác</th>
                </tr>
            </thead>

            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>

                        <td class="customer-order-code">
                            {{ $order->order_code }}
                        </td>

                        <td>
                            <strong>
                                {{ number_format($order->total_amount, 0, ',', '.') }}đ
                            </strong>
                        </td>

                        <td>
                            <span class="customer-order-status {{ $statusClasses[$order->status] ?? '' }}">
                                {{ $statusLabels[$order->status] ?? $order->status }}
                            </span>
                        </td>

                        <td>{{ $order->created_at }}</td>

                        <td>
                            <a class="customer-action"
                               href="{{ route('admin.orders.show', ['id' => $order->id]) }}">
                                Xem đơn hàng
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="customer-empty">
                            Khách hàng chưa có đơn hàng.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="customer-pagination">
        <span>
            Trang {{ $orders->currentPage() }}
            / {{ $orders->lastPage() }}
        </span>

        <div class="customer-page-links">
            @if($orders->previousPageUrl())
                <a href="{{ $orders->previousPageUrl() }}">
                    ← Trang trước
                </a>
            @endif

            @if($orders->nextPageUrl())
                <a href="{{ $orders->nextPageUrl() }}">
                    Trang sau →
                </a>
            @endif
        </div>
    </div>
</section>

@endsection
