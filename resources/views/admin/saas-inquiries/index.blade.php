@extends('admin.layouts.app')
@section('title', 'Yêu cầu tư vấn SaaS - HEXAGON')
@section('topbar_title', 'Yêu cầu tư vấn SaaS')
@section('content')
<div class="hx-inquiry-admin">
    <div class="hx-inquiry-admin-heading"><div><h1>Yêu cầu tư vấn SaaS</h1><p>Theo dõi khách hàng quan tâm tới các dịch vụ của HEXAGON.</p></div></div>
    <div class="hx-inquiry-kpis">
        <div><small>Tổng yêu cầu</small><strong>{{ $counts['all'] }}</strong></div>
        <div><small>Mới tiếp nhận</small><strong>{{ $counts['new'] }}</strong></div>
        <div><small>Đang tư vấn</small><strong>{{ $counts['contacted'] }}</strong></div>
        <div><small>Đã hoàn tất</small><strong>{{ $counts['closed'] }}</strong></div>
    </div>
    <form method="GET" action="{{ route('admin.saas-inquiries.index') }}" class="hx-inquiry-filter">
        <label><span>Tìm kiếm</span><input name="q" value="{{ request('q', '') }}" placeholder="Khách hàng, email, điện thoại, dịch vụ"></label>
        <label><span>Trạng thái</span><select name="status"><option value="">Tất cả</option>@foreach($statuses as $key => $label)<option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>@endforeach</select></label>
        <button type="submit">Lọc yêu cầu</button>
        <a href="{{ route('admin.saas-inquiries.index') }}">Xóa lọc</a>
    </form>
    <div class="hx-inquiry-table-wrap"><table class="hx-inquiry-table"><thead><tr><th>Khách hàng</th><th>Dịch vụ</th><th>Liên hệ</th><th>Trạng thái</th><th>Ngày gửi</th><th></th></tr></thead>
        <tbody>@forelse($inquiries as $item)
            <tr><td><strong>{{ $item->customer_name }}</strong></td><td>{{ $item->service_name }}</td><td>{{ $item->customer_email }}<br><small>{{ $item->customer_phone }}</small></td>
                <td><span class="hx-inquiry-state state-{{ $item->status }}">{{ $statuses[$item->status] ?? $item->status }}</span></td>
                <td>{{ \Illuminate\Support\Carbon::parse($item->created_at)->format('d/m/Y H:i') }}</td>
                <td><a class="hx-inquiry-open" href="{{ route('admin.saas-inquiries.show', ['id' => $item->id]) }}">Xem chi tiết</a></td></tr>
        @empty<tr><td colspan="6" class="hx-inquiry-empty">Chưa có yêu cầu phù hợp.</td></tr>@endforelse</tbody></table></div>
    <div class="hx-inquiry-pagination">{{ $inquiries->links() }}</div>
</div>
@endsection
