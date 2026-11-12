@extends('admin.layouts.app')

@section('title', 'Quản lý đơn hàng - HEXAGON Admin')
@section('topbar_title', 'Quản lý đơn hàng')

@section('content')
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
                    <th>Tổng tiền</th><th>Trạng thái</th><th>Ngày tạo</th>
                </tr></thead>
                <tbody>
                @forelse ($orders as $order)
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
        <div class="pagination-bar">
            <div>@if($orders->previousPageUrl())<a class="text-link" href="{{ $orders->previousPageUrl() }}">← Trang trước</a>@endif</div>
            <span>Trang {{ $orders->currentPage() }} / {{ $orders->lastPage() }}</span>
            <div>@if($orders->nextPageUrl())<a class="text-link" href="{{ $orders->nextPageUrl() }}">Trang sau →</a>@endif</div>
        </div>
    </section>
@endsection
