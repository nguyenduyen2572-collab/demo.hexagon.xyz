@extends('admin.layouts.app')
@section('title', 'Chi tiết tư vấn SaaS - HEXAGON')
@section('topbar_title', 'Chi tiết yêu cầu tư vấn')
@section('content')
<div class="hx-inquiry-admin">
    <div class="hx-inquiry-admin-heading"><div><a class="hx-inquiry-back" href="{{ route('admin.saas-inquiries.index') }}">← Quay lại danh sách</a><h1>Yêu cầu #{{ $inquiry->id }}</h1><p>Được gửi lúc {{ \Illuminate\Support\Carbon::parse($inquiry->created_at)->format('d/m/Y H:i') }}</p></div></div>
    @if(session('success'))<div class="hx-saas-inquiry-alert success" role="status">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="hx-saas-inquiry-alert error" role="alert">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
    <div class="hx-inquiry-detail-grid">
        <section class="hx-inquiry-detail-card"><h2>Thông tin khách hàng</h2>
            <dl><dt>Họ và tên</dt><dd>{{ $inquiry->customer_name }}</dd><dt>Email</dt><dd><a href="mailto:{{ $inquiry->customer_email }}">{{ $inquiry->customer_email }}</a></dd><dt>Số điện thoại</dt><dd>{{ $inquiry->customer_phone }}</dd><dt>Dịch vụ quan tâm</dt><dd>{{ $inquiry->service_name }}</dd><dt>Nhu cầu</dt><dd class="hx-inquiry-message">{{ $inquiry->message ?: 'Khách hàng chưa ghi thêm nhu cầu.' }}</dd></dl>
        </section>
        <section class="hx-inquiry-detail-card"><h2>Xử lý yêu cầu</h2>
            <form action="{{ route('admin.saas-inquiries.update', ['id' => $inquiry->id]) }}" method="POST">@csrf @method('PATCH')
                <label>Trạng thái <select name="status" required>@foreach($statuses as $key => $label)<option value="{{ $key }}" @selected(old('status', $inquiry->status) === $key)>{{ $label }}</option>@endforeach</select></label>
                <label>Ghi chú nội bộ <textarea name="admin_note" rows="7" maxlength="2000" placeholder="Ví dụ: Đã gọi khách hàng, hẹn tư vấn vào thứ sáu...">{{ old('admin_note', $inquiry->admin_note) }}</textarea></label>
                <button type="submit">Lưu cập nhật</button>
            </form>
        </section>
    </div>
</div>
@endsection
