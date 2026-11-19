@extends('admin.layouts.app')
@section('title', 'Chi tiết tư vấn SaaS - HEXAGON')
@section('topbar_title', 'Chi tiết yêu cầu tư vấn')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/hexagon-admin-inquiry-priority-v8-5.css') }}?v=1">
@endpush
@section('content')
<div class="hx-inquiry-admin hx-inquiry-v3">
    <div class="hx-inquiry-admin-heading">
        <div>
            <a class="hx-inquiry-back hx-inquiry-back-v3" href="{{ $backToList }}">← Quay lại danh sách</a>
            <h1>Yêu cầu tư vấn #{{ $inquiry->id }}</h1>
            <p>Được gửi lúc {{ \Illuminate\Support\Carbon::parse($inquiry->created_at)->format('d/m/Y H:i') }}</p>
        </div>
        <span class="hx-inquiry-state state-{{ $inquiry->status }}">{{ $statuses[$inquiry->status] ?? $inquiry->status }}</span>
    </div>

    @if(session('success'))
        <div class="hx-saas-inquiry-alert success" role="status">{{ session('success') }}</div>
    @endif
    @if(session('info'))
        <div class="hx-saas-inquiry-alert" role="status">{{ session('info') }}</div>
    @endif
    @if($errors->any())
        <div class="hx-saas-inquiry-alert error" role="alert">
            @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif

    <div class="hx-inquiry-detail-grid">
        <section class="hx-inquiry-detail-card">
            <h2>Thông tin khách hàng</h2>
            <dl>
                <dt>Họ và tên</dt><dd>{{ $inquiry->customer_name }}</dd>
                <dt>Email</dt><dd><a href="mailto:{{ $inquiry->customer_email }}">{{ $inquiry->customer_email }}</a></dd>
                <dt>Số điện thoại</dt><dd><a href="tel:{{ preg_replace('/[^+0-9]/', '', $inquiry->customer_phone) }}">{{ $inquiry->customer_phone }}</a></dd>
                <dt>Dịch vụ quan tâm</dt><dd>{{ $inquiry->service_name }}</dd>
                <dt>Nhu cầu</dt><dd class="hx-inquiry-message">{{ $inquiry->message ?: 'Khách hàng chưa ghi thêm nhu cầu.' }}</dd>
            </dl>
            @if($inquiry->admin_note)
                <div class="hx-inquiry-last-note">
                    <h3>Ghi chú gần nhất</h3>
                    <p>{{ $inquiry->admin_note }}</p>
                </div>
            @endif
        </section>

        <section class="hx-inquiry-detail-card">
            <h2>Xử lý yêu cầu</h2>
            @php
                $detailDueUtc = $inquiry->follow_up_at ? \Illuminate\Support\Carbon::parse($inquiry->follow_up_at, 'UTC') : null;
                $detailOverdue = $detailDueUtc && $inquiry->status !== 'closed' && $detailDueUtc->lt(\Illuminate\Support\Carbon::now('UTC'));
            @endphp
            @if($detailOverdue)
                <p class="hx-inquiry-v85-detail-notice" role="status">Lịch liên hệ đã quá hạn. Vui lòng liên hệ khách hoặc cập nhật lịch hẹn mới.</p>
            @endif
            <form action="{{ route('admin.saas-inquiries.update', ['id' => $inquiry->id] + $returnFilters) }}" method="POST">
                @csrf
                @method('PATCH')
                <label>Trạng thái
                    <select name="status" required>
                        @foreach($statuses as $key => $label)
                            <option value="{{ $key }}" @selected(old('status', $inquiry->status) === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>Người phụ trách
                    <select name="assigned_admin_id">
                        <option value="">Chưa phân công</option>
                        @foreach($admins as $admin)
                            <option value="{{ $admin->id }}" @selected((string) old('assigned_admin_id', $inquiry->assigned_admin_id) === (string) $admin->id)>{{ $admin->name }} ({{ $admin->email }})</option>
                        @endforeach
                    </select>
                    <small class="hx-inquiry-hint">Chọn tài khoản Admin để giao yêu cầu. Nhấn “Lưu và ghi lịch sử” để hoàn tất phân công; tên người phụ trách sẽ hiển thị trong danh sách.</small>
                </label>
                <label>Lịch liên hệ tiếp theo <span class="hx-inquiry-optional">(giờ Việt Nam)</span>
                    @php
                        $scheduledValue = $inquiry->follow_up_at
                            ? \Illuminate\Support\Carbon::parse($inquiry->follow_up_at, 'UTC')->timezone('Asia/Ho_Chi_Minh')->format('Y-m-d\TH:i')
                            : '';
                    @endphp
                    <input type="datetime-local" name="follow_up_at" value="{{ old('follow_up_at', $scheduledValue) }}">
                    <small class="hx-inquiry-hint">Để trống nếu chưa hẹn. Chọn “Đã hoàn tất” sẽ xóa lịch hẹn đang chờ.</small>
                </label>
                <label>Thêm ghi chú xử lý mới <span class="hx-inquiry-optional">(không bắt buộc)</span>
                    <textarea name="admin_note" rows="5" maxlength="2000" placeholder="Ví dụ: Đã gọi khách hàng, hẹn giới thiệu sản phẩm vào thứ sáu...">{{ old('admin_note') }}</textarea>
                    <small class="hx-inquiry-hint">Ghi chú mới sẽ được lưu vào lịch sử, không xóa các ghi chú cũ. Để trống nếu không có ghi chú mới.</small>
                </label>
                <button type="submit">Lưu và ghi lịch sử</button>
            </form>
        </section>
    </div>

    <section class="hx-inquiry-detail-card hx-inquiry-activity-card" aria-labelledby="inquiry-activity-heading">
        <div class="hx-inquiry-activity-header">
            <div><h2 id="inquiry-activity-heading">Lịch sử chăm sóc khách hàng</h2>
                <p>Theo dõi các lần cập nhật trạng thái, phân công, lịch hẹn và ghi chú của Admin.</p>
            </div>
        </div>
        @forelse($activities as $activity)
            <article class="hx-inquiry-activity">
                <div class="hx-inquiry-activity-top">
                    <strong>{{ $activity->admin_name }}</strong>
                    <time datetime="{{ \Illuminate\Support\Carbon::parse($activity->created_at, 'UTC')->toIso8601String() }}">
                        {{ \Illuminate\Support\Carbon::parse($activity->created_at, 'UTC')->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i') }}
                    </time>
                </div>
                @if($activity->status_to !== null)
                    <p><strong>Trạng thái:</strong> {{ $statuses[$activity->status_from] ?? $activity->status_from }} → {{ $statuses[$activity->status_to] ?? $activity->status_to }}</p>
                @endif
                @if($activity->assignee_to !== null)
                    <p><strong>Phụ trách:</strong> {{ $activity->assignee_from }} → {{ $activity->assignee_to }}</p>
                @endif
                @if($activity->follow_up_from !== null || $activity->follow_up_to !== null)
                    <p><strong>Lịch hẹn:</strong>
                        {{ $activity->follow_up_from ? \Illuminate\Support\Carbon::parse($activity->follow_up_from, 'UTC')->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i') : 'Chưa có' }}
                        →
                        {{ $activity->follow_up_to ? \Illuminate\Support\Carbon::parse($activity->follow_up_to, 'UTC')->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i') : 'Không còn lịch hẹn' }}
                    </p>
                @endif
                @if($activity->note !== null)
                    <p class="hx-inquiry-activity-note">{{ $activity->note }}</p>
                @endif
            </article>
        @empty
            <p class="hx-inquiry-activity-empty">Chưa có lần xử lý nào được ghi nhận sau nâng cấp. Những ghi chú cũ vẫn hiển thị ở mục “Ghi chú gần nhất”.</p>
        @endforelse
        <div class="hx-inquiry-pagination">{{ $activities->links() }}</div>
    </section>
</div>
@endsection
