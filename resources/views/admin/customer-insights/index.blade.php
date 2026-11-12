@extends('admin.layouts.app')

@section('title', 'Quản lý khách hàng - HEXAGON Admin')
@section('topbar_title', 'Khách hàng')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/hexagon-admin-customers-v2.css') }}?v=2">
@endpush

@section('content')
<section class="hxc-page" aria-labelledby="hxc-title">
    <header class="hxc-hero">
        <div>
            <span class="hxc-eyebrow">QUẢN LÝ TÀI KHOẢN</span>
            <h1 id="hxc-title">Khách hàng <span>HEXAGON</span></h1>
            <p>Tìm kiếm, theo dõi hoạt động đặt hàng và kiểm tra tình trạng xác minh tài khoản.</p>
        </div>
        <span class="hxc-hero-symbol" aria-hidden="true">✦</span>
    </header>

    <div class="hxc-stats" aria-label="Thống kê khách hàng">
        <div class="hxc-stat"><span>Tài khoản khách hàng</span><strong>{{ number_format($overview['all'], 0, ',', '.') }}</strong><small>Không bao gồm tài khoản Admin</small></div>
        <div class="hxc-stat"><span>Đã xác minh email</span><strong>{{ number_format($overview['verified'], 0, ',', '.') }}</strong><small>Đã có thời gian xác minh</small></div>
        <div class="hxc-stat"><span>Đã có đơn hàng</span><strong>{{ number_format($overview['ordered'], 0, ',', '.') }}</strong><small>Đơn liên kết với tài khoản</small></div>
    </div>

    <section class="hxc-panel" aria-labelledby="hxc-filter-title">
        <div class="hxc-panel-head"><div><span class="hxc-eyebrow">TÌM KIẾM THÔNG MINH</span><h2 id="hxc-filter-title">Bộ lọc khách hàng</h2></div><span class="hxc-count">{{ number_format($customers->total(), 0, ',', '.') }} kết quả</span></div>
        <form class="hxc-form" method="GET" action="{{ route('admin.customers.index') }}">
            <label class="hxc-field hxc-search"><span>Họ tên hoặc email</span><input type="search" name="q" value="{{ $filters['q'] }}" placeholder="Nhập thông tin khách hàng" maxlength="120" autocomplete="off"></label>
            <label class="hxc-field"><span>Xác minh email</span><select name="verified">
                <option value="all" @selected($filters['verified'] === 'all')>Tất cả</option>
                <option value="yes" @selected($filters['verified'] === 'yes')>Đã xác minh</option>
                <option value="no" @selected($filters['verified'] === 'no')>Chưa xác minh</option>
            </select></label>
            <label class="hxc-field"><span>Đơn hàng</span><select name="orders">
                <option value="all" @selected($filters['orders'] === 'all')>Tất cả</option>
                <option value="yes" @selected($filters['orders'] === 'yes')>Đã đặt hàng</option>
                <option value="no" @selected($filters['orders'] === 'no')>Chưa đặt hàng</option>
            </select></label>
            <label class="hxc-field"><span>Sắp xếp</span><select name="sort">
                <option value="latest" @selected($filters['sort'] === 'latest')>Mới đăng ký</option>
                <option value="oldest" @selected($filters['sort'] === 'oldest')>Đăng ký lâu nhất</option>
                <option value="orders" @selected($filters['sort'] === 'orders')>Nhiều đơn nhất</option>
                <option value="value" @selected($filters['sort'] === 'value')>Giá trị đơn cao nhất</option>
            </select></label>
            <div class="hxc-actions"><button type="submit" class="hxc-btn hxc-btn-primary">Áp dụng bộ lọc</button><a class="hxc-btn hxc-btn-light" href="{{ route('admin.customers.index') }}">Đặt lại</a></div>
        </form>
    </section>

    <section class="hxc-panel" aria-labelledby="hxc-table-title">
        <div class="hxc-panel-head"><div><span class="hxc-eyebrow">DANH SÁCH TÀI KHOẢN</span><h2 id="hxc-table-title">Khách hàng đã đăng ký</h2></div><span class="hxc-count">Trang {{ $customers->currentPage() }} / {{ $customers->lastPage() }}</span></div>
        <div class="hxc-table-wrap">
            <table class="hxc-table">
                <thead><tr><th>Khách hàng</th><th>Xác minh</th><th>Đơn hàng</th><th>Giá trị đơn chưa hủy</th><th>Ngày đăng ký</th><th></th></tr></thead>
                <tbody>
                @forelse($customers as $customer)
                    <tr>
                        <td><div class="hxc-identity"><span class="hxc-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr(trim((string) $customer->name), 0, 1)) ?: 'K' }}</span><div><strong>{{ $customer->name }}</strong><small>{{ $customer->email }}</small></div></div></td>
                        <td>@if($customer->email_verified_at)<span class="hxc-pill hxc-pill-success">Đã xác minh</span>@else<span class="hxc-pill hxc-pill-warn">Chưa xác minh</span>@endif</td>
                        <td><strong>{{ number_format((int) $customer->orders_count, 0, ',', '.') }}</strong></td>
                        <td><strong class="hxc-value">{{ number_format((float) $customer->orders_value, 0, ',', '.') }}đ</strong></td>
                        <td>{{ \Illuminate\Support\Carbon::parse($customer->created_at)->format('d/m/Y') }}</td>
                        <td><a class="hxc-detail-link" href="{{ route('admin.customers.show', ['id' => $customer->id]) }}" aria-label="Xem chi tiết {{ $customer->name }}">Chi tiết →</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="hxc-empty">Không tìm thấy khách hàng phù hợp. Hãy thử điều chỉnh bộ lọc.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($customers->hasPages())
            <div class="hxc-pagination">{{ $customers->links() }}</div>
        @endif
    </section>

</section>
@endsection
