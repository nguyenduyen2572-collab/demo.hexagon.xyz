@extends('admin.layouts.app')

@section('title', 'Chi tiết khách hàng - HEXAGON Admin')
@section('topbar_title', 'Chi tiết khách hàng')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/hexagon-admin-customers-v2.css') }}?v=2">
@endpush

@section('content')
<section class="hxc-page" aria-labelledby="hxc-title">
    <a class="hxc-back" href="{{ route('admin.customers.index') }}">← Quay lại danh sách khách hàng</a>
    <header class="hxc-hero hxc-hero-detail">
        <div><span class="hxc-eyebrow">HỒ SƠ KHÁCH HÀNG</span><h1 id="hxc-title">{{ $customer->name }}</h1><p>{{ $customer->email }}</p></div>
        @if($customer->email_verified_at)<span class="hxc-pill hxc-pill-success">Đã xác minh email</span>@else<span class="hxc-pill hxc-pill-warn">Chưa xác minh email</span>@endif
    </header>

    <div class="hxc-stats" aria-label="Thống kê đơn hàng khách hàng">
        <div class="hxc-stat"><span>Tổng số đơn hàng</span><strong>{{ number_format($summary['count'], 0, ',', '.') }}</strong><small>Liên kết với tài khoản này</small></div>
        <div class="hxc-stat"><span>Đã xử lý</span><strong>{{ number_format($summary['completed'], 0, ',', '.') }}</strong><small>Trạng thái hoàn tất</small></div>
        <div class="hxc-stat"><span>Đơn đã hủy</span><strong>{{ number_format($summary['cancelled'], 0, ',', '.') }}</strong><small>Không tính vào giá trị</small></div>
        <div class="hxc-stat"><span>Giá trị đơn chưa hủy</span><strong class="hxc-stat-money">{{ number_format((float) $summary['amount'], 0, ',', '.') }}đ</strong><small>Không phải tiền đã thu</small></div>
    </div>

    <section class="hxc-panel" aria-labelledby="hxc-profile-title">
        <div class="hxc-panel-head"><div><span class="hxc-eyebrow">THÔNG TIN TÀI KHOẢN</span><h2 id="hxc-profile-title">Hồ sơ đăng ký</h2></div></div>
        <dl class="hxc-profile">
            <div><dt>Mã tài khoản</dt><dd>#{{ $customer->id }}</dd></div>
            <div><dt>Họ và tên</dt><dd>{{ $customer->name }}</dd></div>
            <div><dt>Địa chỉ email</dt><dd>{{ $customer->email }}</dd></div>
            <div><dt>Đăng ký lúc</dt><dd>{{ \Illuminate\Support\Carbon::parse($customer->created_at)->format('d/m/Y H:i') }}</dd></div>
            <div><dt>Xác minh email</dt><dd>{{ $customer->email_verified_at ? \Illuminate\Support\Carbon::parse($customer->email_verified_at)->format('d/m/Y H:i') : 'Chưa xác minh' }}</dd></div>
        </dl>
    </section>

    <section class="hxc-panel" aria-labelledby="hxc-orders-title">
        <div class="hxc-panel-head"><div><span class="hxc-eyebrow">LỊCH SỬ GIAO DỊCH</span><h2 id="hxc-orders-title">Đơn hàng của khách</h2></div><span class="hxc-count">{{ number_format($orders->total(), 0, ',', '.') }} đơn</span></div>
        <div class="hxc-table-wrap">
            <table class="hxc-table">
                <thead><tr><th>Đơn hàng</th><th>Ngày đặt</th><th>Trạng thái</th><th>Giá trị</th><th></th></tr></thead>
                <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td><strong>#{{ $order->id }}</strong><small class="hxc-code">{{ $order->order_code }}</small></td>
                        <td>{{ \Illuminate\Support\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}</td>
                        <td><span class="hxc-pill {{ $order->status === 'cancelled_demo' ? 'hxc-pill-warn' : 'hxc-pill-neutral' }}">{{ $statusLabels[$order->status] ?? $order->status }}</span></td>
                        <td><strong>{{ number_format((float) $order->total_amount, 0, ',', '.') }}đ</strong></td>
                        <td><a class="hxc-detail-link" href="{{ route('admin.orders.show', ['id' => $order->id]) }}">Xem đơn →</a></td>
                    </tr>
                @empty
                    <tr><td class="hxc-empty" colspan="5">Tài khoản này chưa có đơn hàng nào được liên kết.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())<div class="hxc-pagination">{{ $orders->links() }}</div>@endif
    </section>
    <p class="hxc-note">Chỉ xem dữ liệu, không có thao tác thay đổi hoặc xóa tài khoản. Các đơn đặt khi chưa đăng nhập không tự động được nối vào hồ sơ theo email.</p>
</section>
@endsection
