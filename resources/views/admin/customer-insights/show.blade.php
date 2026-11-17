@extends('admin.layouts.app')

@section('title', 'Chi tiết khách hàng - HEXAGON Admin')
@section('topbar_title', 'Chi tiết khách hàng')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/hexagon-admin-customers-v2.css') }}?v=2">
<link rel="stylesheet" href="{{ asset('css/hexagon-admin-customers-compact-v2.css') }}?v=1">
@endpush

@section('content')
<section class="hxc-page hxc-compact-v2" aria-labelledby="hxc-title">
    {{-- HEXAGON-CUSTOMER-DETAIL-COMPACT-V2 --}}
    <a class="hxc-back" href="{{ route('admin.customers.index') }}">← Quay lại danh sách khách hàng</a>
    {{-- HEXAGON-CUSTOMER-DETAIL-CLEAN-V1 --}}
    <header class="hxc-page-heading hxc-page-heading-detail"><div><h1 id="hxc-title">{{ $customer->name }}</h1><p>{{ $customer->email }}</p></div></header>

    <div class="hxc-stats hxc-detail-stats" aria-label="Tổng quan đơn hàng">
        <div class="hxc-stat"><span>Tổng đơn hàng</span><strong>{{ number_format($summary['count'], 0, ',', '.') }}</strong></div>
        <div class="hxc-stat"><span>Cần xử lý</span><strong>{{ number_format($summary['needs_action'], 0, ',', '.') }}</strong><small>Chờ xử lý hoặc đang xử lý</small></div>
        <div class="hxc-stat"><span>Hoàn tất</span><strong>{{ number_format($summary['completed'], 0, ',', '.') }}</strong></div>
        <div class="hxc-stat"><span>Đã hủy</span><strong>{{ number_format($summary['cancelled'], 0, ',', '.') }}</strong></div>
    </div>

    <section class="hxc-panel" aria-labelledby="hxc-profile-title">
        <div class="hxc-panel-head"><div><span class="hxc-eyebrow">THÔNG TIN TÀI KHOẢN</span><h2 id="hxc-profile-title">Hồ sơ đăng ký</h2></div></div>
        <dl class="hxc-profile">
            <div><dt>Mã tài khoản</dt><dd>#{{ $customer->id }}</dd></div>
            <div><dt>Họ và tên</dt><dd>{{ $customer->name }}</dd></div>
            <div><dt>Địa chỉ email</dt><dd>{{ $customer->email }}</dd></div>
            <div><dt>Đăng ký lúc</dt><dd>{{ \Illuminate\Support\Carbon::parse($customer->created_at)->format('d/m/Y H:i') }}</dd></div>

        </dl>
    </section>

    <section class="hxc-panel" aria-labelledby="hxc-orders-title">
        <div class="hxc-panel-head"><div><span class="hxc-eyebrow">ĐƠN HÀNG</span><h2 id="hxc-orders-title">Danh sách đơn hàng</h2></div><span class="hxc-count">{{ number_format($orders->total(), 0, ',', '.') }} đơn</span></div>
        <div class="hxc-table-wrap">
            <table class="hxc-table">
                <thead><tr><th>Đơn hàng</th><th>Ngày đặt</th><th>Trạng thái</th><th>Tổng tiền</th><th></th></tr></thead>
                <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td><strong class="hxc-code">{{ \App\Support\HexagonOrderCode::forOrder($order) }}</strong></td>
                        <td>{{ \Illuminate\Support\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}</td>
                        <td><span class="hxc-pill {{ $order->status === 'cancelled_demo' ? 'hxc-status-cancelled' : ($order->status === 'completed_demo' ? 'hxc-status-completed' : ($order->status === 'processing_demo' ? 'hxc-status-processing' : 'hxc-status-pending')) }}">{{ $statusLabels[$order->status] ?? $order->status }}</span></td>
                        <td><strong>{{ number_format((float) $order->total_amount, 0, ',', '.') }}đ</strong></td>
                        <td><a class="hxc-detail-link" href="{{ route('admin.orders.show', ['id' => $order->id]) }}">Xem chi tiết →</a></td>
                    </tr>
                @empty
                    <tr><td class="hxc-empty" colspan="5">Tài khoản này chưa có đơn hàng nào được liên kết.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())<div class="hxc-pagination">{{ $orders->links() }}</div>@endif
    </section>

</section>
@endsection
