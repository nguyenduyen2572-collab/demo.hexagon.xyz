@extends('customer.layouts.account-shell')

@section('title', 'Đơn hàng của tôi - HEXAGON')

@section('content')
<div class="hx-page-heading">
    <div>
        <p class="hx-eyebrow" data-vi="LỊCH SỬ ĐẶT HÀNG" data-en="ORDER HISTORY">LỊCH SỬ ĐẶT HÀNG</p>
        <h1 data-vi="Đơn hàng của tôi" data-en="My orders">Đơn hàng của tôi</h1>
        <p data-vi="Theo dõi những đơn hàng đã liên kết với tài khoản của bạn." data-en="Track orders linked to your account.">Theo dõi những đơn hàng đã liên kết với tài khoản của bạn.</p>
    </div>
    <span class="hx-order-count">{{ $orders->total() }} <span data-vi="đơn hàng" data-en="orders">đơn hàng</span></span>
</div>

<section class="hx-card hx-orders-card">
    @php
        $statusLabels = ['pending_demo' => 'Chờ xử lý', 'processing_demo' => 'Đang xử lý', 'completed_demo' => 'Đã xử lý', 'cancelled_demo' => 'Đã hủy'];
        $statusEnglish = ['pending_demo' => 'Pending', 'processing_demo' => 'Processing', 'completed_demo' => 'Completed', 'cancelled_demo' => 'Cancelled'];
        $statusCss = ['pending_demo' => 'pending', 'processing_demo' => 'processing', 'completed_demo' => 'completed', 'cancelled_demo' => 'cancelled'];
    @endphp
    <div class="hx-table-scroll">
        <table class="hx-orders-table">
            <thead>
                <tr>
                    <th data-vi="Mã đơn hàng" data-en="Order ID">Mã đơn hàng</th>
                    <th data-vi="Tổng giá trị" data-en="Total">Tổng giá trị</th>
                    <th data-vi="Trạng thái" data-en="Status">Trạng thái</th>
                    <th data-vi="Ngày tạo" data-en="Created">Ngày tạo</th>
                    <th data-vi="Chi tiết" data-en="Details">Chi tiết</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td class="hx-order-code">{{ $order->order_code }}</td>
                        <td><strong>{{ number_format($order->total_amount, 0, ',', '.') }}đ</strong></td>
                        <td><span class="hx-status hx-status-{{ $statusCss[$order->status] ?? 'unknown' }}"
                                  data-vi="{{ $statusLabels[$order->status] ?? $order->status }}"
                                  data-en="{{ $statusEnglish[$order->status] ?? $order->status }}">{{ $statusLabels[$order->status] ?? $order->status }}</span></td>
                        <td>{{ $order->created_at }}</td>
                        <td><a class="hx-detail-link" href="{{ route('customer.account.orders.show', ['code' => $order->order_code]) }}"><span data-vi="Xem chi tiết" data-en="View details">Xem chi tiết</span></a></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="hx-empty" data-vi="Bạn chưa có đơn hàng nào liên kết với tài khoản này." data-en="No orders are linked to this account yet.">Bạn chưa có đơn hàng nào liên kết với tài khoản này.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($orders->hasPages())
        <div class="hx-pagination">
            <span>{{ $orders->currentPage() }} / {{ $orders->lastPage() }}</span>
            <div>
                @if($orders->previousPageUrl())<a href="{{ $orders->previousPageUrl() }}" data-vi="Trang trước" data-en="Previous">Trang trước</a>@endif
                @if($orders->nextPageUrl())<a href="{{ $orders->nextPageUrl() }}" data-vi="Trang sau" data-en="Next">Trang sau</a>@endif
            </div>
        </div>
    @endif
</section>
@endsection
