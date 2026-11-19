@extends('admin.layouts.app')

@section('title', 'Quản lý đơn hàng - HEXAGON Admin')
@section('topbar_title', 'Quản lý đơn hàng')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/hexagon-admin-order-actions-v1-1.css') }}?v=1">
    <link rel="stylesheet" href="{{ asset('css/hexagon-admin-assignment-v9-2.css') }}?v=1">
    <div class="page-heading">
        <h1>Danh sách đơn hàng</h1>
        <p>Theo dõi, tra cứu và cập nhật trạng thái các đơn hàng.</p>
    </div>

    @include('admin.partials.order-filters-v1')
    <section class="panel">
        <div class="table-wrap">
            <table class="admin-table">
                <thead><tr>
                    <th>Đơn hàng</th><th>Khách hàng</th><th>Email</th>
                    <th>Tổng tiền</th><th>Trạng thái</th><th>Ngày tạo</th><th class="hx-order-actions-v11">Thao tác</th>
                </tr></thead>
                <tbody>
                @forelse ($orders as $order)
                    <tr>
                        <td class="code-cell"><a class="text-link" href="{{ route('admin.orders.show', array_merge(['id' => $order->id], \App\Support\HexagonAdminOrderNavigation::filters(request()))) }}">{{ \App\Support\HexagonOrderCode::forOrder($order) }}</a>
                            <small class="hx-order-assignee">Phụ trách: {{ $assignedNames[$order->assigned_admin_id] ?? 'Chưa phân công' }}</small></td>
                        <td>{{ $order->customer_name }}</td>
                        <td>{{ $order->customer_email }}</td>
                        <td><strong>{{ number_format($order->total_amount, 0, ',', '.') }}đ</strong></td>
                        <td><x-admin.status :status="$order->status" /></td>
                        <td>{{ \Illuminate\Support\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}</td>
                        <td class="hx-order-actions-v11">
                            <a class="hx-order-action-v11 {{ in_array($order->status, ['pending_demo', 'processing_demo'], true) ? 'hx-order-action-v11--pending' : '' }}"
                               href="{{ route('admin.orders.show', array_merge(['id' => $order->id], \App\Support\HexagonAdminOrderNavigation::filters(request()))) }}"
                               aria-label="Xem chi tiết đơn {{ \App\Support\HexagonOrderCode::forOrder($order) }}">
                                {{ in_array($order->status, ['pending_demo', 'processing_demo'], true) ? 'Xem & xử lý' : 'Xem chi tiết' }}
                                <span aria-hidden="true">→</span>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty-cell">Không có đơn hàng phù hợp.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-bar">
            <div>@if($orders->previousPageUrl())<a class="text-link" href="{{ $orders->previousPageUrl() }}">← Trang trước</a>@endif</div>
            <span>Trang {{ $orders->currentPage() }} / {{ $orders->lastPage() }}</span>
            <div>@if($orders->nextPageUrl())<a class="text-link" href="{{ $orders->nextPageUrl() }}">Trang sau →</a>@endif</div>
        </div>
    </section>
@endsection
