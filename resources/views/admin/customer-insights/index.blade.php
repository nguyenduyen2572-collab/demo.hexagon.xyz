@extends('admin.layouts.app')

@section('title', 'Quản lý khách hàng - HEXAGON Admin')
@section('topbar_title', 'Khách hàng')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/hexagon-admin-customers-v2.css') }}?v=2">
<link rel="stylesheet" href="{{ asset('css/hexagon-admin-customers-compact-v2.css') }}?v=1">
<link rel="stylesheet" href="{{ asset('css/hexagon-admin-pricing-customers-v5.css') }}?v=5">
@endpush

@section('content')
<section class="hxc-page hxc-compact-v2 hxv5-customer-list" aria-labelledby="hxc-title">
    {{-- HEXAGON-CUSTOMERS-COMPACT-V2 --}}
    {{-- HEXAGON-CUSTOMERS-CLEAN-V1 --}}
    <header class="hxc-page-heading"><div><h1 id="hxc-title">Khách hàng</h1><p>Tra cứu tài khoản và xem đơn hàng của từng khách.</p></div></header>

    <section class="hxc-panel" aria-labelledby="hxc-filter-title">
        <div class="hxc-panel-head"><div><span class="hxc-eyebrow">TRA CỨU</span><h2 id="hxc-filter-title">Tìm khách hàng</h2></div></div>
        <form class="hxc-form" method="GET" action="{{ route('admin.customers.index') }}">
            <label class="hxc-field hxc-search"><span>Họ tên hoặc email</span><input type="search" name="q" value="{{ $filters['q'] }}" placeholder="Nhập thông tin khách hàng" maxlength="120" autocomplete="off"></label>
            <label class="hxc-field"><span>Sắp xếp</span><select name="sort">
                <option value="latest" @selected($filters['sort'] === 'latest')>Mới đăng ký</option>
                <option value="oldest" @selected($filters['sort'] === 'oldest')>Đăng ký lâu nhất</option>
                <option value="orders" @selected($filters['sort'] === 'orders')>Nhiều đơn nhất</option>
            </select></label>
            <label class="hxc-field"><span>Tình trạng đơn</span><select name="activity">
                <option value="all" @selected($filters['activity'] === 'all')>Tất cả khách hàng</option>
                <option value="needs_action" @selected($filters['activity'] === 'needs_action')>Có đơn cần xử lý</option>
                <option value="no_orders" @selected($filters['activity'] === 'no_orders')>Chưa có đơn</option>
            </select></label>
            <div class="hxc-actions"><button type="submit" class="hxc-btn hxc-btn-primary">Tìm kiếm</button><a class="hxc-btn hxc-btn-light" href="{{ route('admin.customers.index') }}">Đặt lại</a></div>
        </form>
    </section>

    <section class="hxc-panel" aria-labelledby="hxc-table-title">
        <div class="hxc-panel-head"><div><span class="hxc-eyebrow">DANH SÁCH</span><h2 id="hxc-table-title">Khách hàng</h2></div><span class="hxc-count">{{ number_format($customers->total(), 0, ',', '.') }} khách hàng</span></div>
        <div class="hxc-table-wrap">
            <table class="hxc-table">
                <thead><tr><th>Khách hàng</th><th>Số đơn</th><th>Cần xử lý</th><th>Ngày đăng ký</th><th>Thao tác</th></tr></thead>
                <tbody>
                @forelse($customers as $customer)
                    <tr>
                        <td><div class="hxc-identity"><span class="hxc-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr(trim((string) $customer->name), 0, 1)) ?: 'K' }}</span><div><strong>{{ $customer->name }}</strong><small>{{ $customer->email }}</small></div></div></td>
                        <td><strong>{{ number_format((int) $customer->orders_count, 0, ',', '.') }}</strong></td>
                        <td>
                            @if((int) $customer->needs_action_count > 0)
                                <a class="hxv5-need-count" href="{{ route('admin.customers.show', ['id' => $customer->id, 'status' => 'needs_action']) }}" aria-label="Xem {{ $customer->needs_action_count }} đơn cần xử lý của {{ $customer->name }}">{{ number_format((int) $customer->needs_action_count, 0, ',', '.') }} đơn</a>
                            @else
                                <span class="hxv5-muted-count">—</span>
                            @endif
                        </td>
                        <td>{{ \Illuminate\Support\Carbon::parse($customer->created_at)->format('d/m/Y') }}</td>
                        <td><a class="hxc-detail-link" href="{{ route('admin.customers.show', ['id' => $customer->id]) }}" aria-label="Xem chi tiết {{ $customer->name }}">Chi tiết →</a></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="hxc-empty">Không tìm thấy khách hàng phù hợp. Hãy thử điều chỉnh bộ lọc.</td></tr>
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
