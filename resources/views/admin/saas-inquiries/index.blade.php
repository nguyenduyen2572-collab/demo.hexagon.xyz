@extends('admin.layouts.app')
@section('title', 'Yêu cầu tư vấn SaaS - HEXAGON')
@section('topbar_title', 'Yêu cầu tư vấn SaaS')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/hexagon-admin-inquiry-priority-v8-5.css') }}?v=1">
@endpush
@section('content')
<div class="hx-inquiry-admin hx-inquiry-v3">
    <div class="hx-inquiry-admin-heading">
        <div>
            <h1>Yêu cầu tư vấn SaaS</h1>
            <p>Theo dõi yêu cầu mới, phân công tư vấn và lịch liên hệ với khách hàng.</p>
        </div>
    </div>

    <div class="hx-inquiry-kpis hx-inquiry-kpis-v3" aria-label="Tổng hợp yêu cầu tư vấn">
        <a class="hx-inquiry-kpi" href="{{ route('admin.saas-inquiries.index') }}">
            <small>Tổng yêu cầu</small><strong>{{ $counts['all'] }}</strong>
        </a>
        <a class="hx-inquiry-kpi" href="{{ route('admin.saas-inquiries.index', ['status' => 'new']) }}">
            <small>Mới tiếp nhận</small><strong>{{ $counts['new'] }}</strong>
        </a>
        <a class="hx-inquiry-kpi" href="{{ route('admin.saas-inquiries.index', ['status' => 'contacted']) }}">
            <small>Đang tư vấn</small><strong>{{ $counts['contacted'] }}</strong>
        </a>
        <a class="hx-inquiry-kpi" href="{{ route('admin.saas-inquiries.index', ['due' => 'overdue']) }}">
            <small>Hẹn đã quá hạn</small><strong>{{ $counts['overdue'] }}</strong>
        </a>
        <a class="hx-inquiry-kpi" href="{{ route('admin.saas-inquiries.index', ['status' => 'closed']) }}">
            <small>Đã hoàn tất</small><strong>{{ $counts['closed'] }}</strong>
        </a>
    </div>

    <form method="GET" action="{{ route('admin.saas-inquiries.index') }}" class="hx-inquiry-filter hx-inquiry-filter-v3">
        <label><span>Tìm kiếm</span>
            <input type="search" name="q" value="{{ request('q', '') }}" maxlength="100" placeholder="Khách hàng, email, điện thoại, dịch vụ">
        </label>
        <label><span>Trạng thái</span>
            <select name="status"><option value="">Tất cả</option>
                @foreach($statuses as $key => $label)
                    <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </label>
        <label><span>Người phụ trách</span>
            <select name="assigned">
                <option value="">Tất cả</option>
                <option value="mine" @selected(request('assigned') === 'mine')>Tôi phụ trách</option>
                <option value="unassigned" @selected(request('assigned') === 'unassigned')>Chưa phân công</option>
            </select>
        </label>
        <label><span>Lịch liên hệ</span>
            <select name="due">
                <option value="">Tất cả</option>
                <option value="overdue" @selected(request('due') === 'overdue')>Đã quá hạn</option>
                <option value="today" @selected(request('due') === 'today')>Hôm nay</option>
                <option value="next7" @selected(request('due') === 'next7')>Trong 7 ngày tới</option>
            </select>
        </label>
        <label><span>Sắp xếp</span>
            <select name="sort">
                <option value="priority" @selected(request('sort', 'priority') === 'priority')>Việc cần ưu tiên</option>
                <option value="next" @selected(request('sort') === 'next')>Lịch hẹn gần nhất</option>
                <option value="newest" @selected(request('sort') === 'newest')>Mới gửi gần đây</option>
            </select>
        </label>
        <button type="submit">Áp dụng bộ lọc</button>
        <a href="{{ route('admin.saas-inquiries.index') }}">Xóa lọc</a>
    </form>

    <p class="hx-inquiry-v85-count" role="status">Tìm thấy <strong>{{ $inquiries->total() }}</strong> yêu cầu. Các lịch quá hạn được đánh dấu ngay trong danh sách.</p>

    <div class="hx-inquiry-table-wrap">
        <table class="hx-inquiry-table hx-inquiry-table-v3">
            <thead><tr>
                <th scope="col">Khách hàng</th><th scope="col">Dịch vụ</th>
                <th scope="col">Trạng thái</th><th scope="col">Phụ trách</th>
                <th scope="col">Hẹn liên hệ</th><th scope="col">Ngày gửi</th>
                <th scope="col">Thao tác</th>
            </tr></thead>
            <tbody>
            @forelse($inquiries as $item)
                @php
                    $followUp = $item->follow_up_at
                        ? \Illuminate\Support\Carbon::parse($item->follow_up_at, 'UTC')->timezone('Asia/Ho_Chi_Minh')
                        : null;
                    $dueUtc = $item->follow_up_at ? \Illuminate\Support\Carbon::parse($item->follow_up_at, 'UTC') : null;
                    $isOverdue = $dueUtc && $item->status !== 'closed' && $dueUtc->lt(\Illuminate\Support\Carbon::now('UTC'));
                    $isSoon = $dueUtc && $item->status !== 'closed' && ! $isOverdue
                        && $dueUtc->lt(\Illuminate\Support\Carbon::now('UTC')->addDays(7));
                @endphp
                <tr @class(['hx-inquiry-v85-overdue-row' => $isOverdue])>
                    <td><strong>{{ $item->customer_name }}</strong><small class="hx-inquiry-secondary"><a href="mailto:{{ $item->customer_email }}">{{ $item->customer_email }}</a></small></td>
                    <td>{{ $item->service_name }}</td>
                    <td><span class="hx-inquiry-state state-{{ $item->status }}">{{ $statuses[$item->status] ?? $item->status }}</span></td>
                    <td>{{ $item->assigned_name ?: 'Chưa phân công' }}</td>
                    <td>
                        @if($followUp)
                            <time datetime="{{ $followUp->toIso8601String() }}">{{ $followUp->format('d/m/Y H:i') }}</time>
                            @if($isOverdue)<span class="hx-inquiry-overdue">Quá hạn — cần liên hệ</span>
                            @elseif($isSoon)<span class="hx-inquiry-v85-soon">Trong 7 ngày tới</span>@endif
                        @else
                            <span class="hx-inquiry-muted">Chưa có lịch</span>
                        @endif
                    </td>
                    <td>{{ \Illuminate\Support\Carbon::parse($item->created_at)->format('d/m/Y H:i') }}</td>
                    <td><a class="hx-inquiry-open" href="{{ route('admin.saas-inquiries.show', ['id' => $item->id] + request()->only(['q', 'status', 'assigned', 'due', 'sort', 'page'])) }}">Xử lý yêu cầu</a></td>
                </tr>
            @empty
                <tr><td colspan="7" class="hx-inquiry-empty">Chưa có yêu cầu phù hợp với bộ lọc.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="hx-inquiry-pagination">{{ $inquiries->links() }}</div>
    <p class="hx-inquiry-help">Giờ hẹn hiển thị theo múi giờ Việt Nam (GMT+7). Số yêu cầu mới trên menu được cập nhật khi tải trang.</p>
</div>
@endsection
