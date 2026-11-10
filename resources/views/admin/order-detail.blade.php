@extends('admin.layouts.app')

@section('title', 'Chi tiết đơn hàng - HEXAGON Admin')
@section('topbar_title', 'Chi tiết đơn hàng')

@section('content')
    <a class="back-button" href="{{ route('admin.orders.index') }}">← Quay lại danh sách</a>

    <div class="page-heading">
        <h1>Đơn hàng #{{ $order->id }}</h1>
        <p class="order-code-line">Mã đơn: {{ $order->order_code }}</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success" role="status">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-error" role="alert">{{ $errors->first() }}</div>
    @endif

    <div class="detail-grid">
        <div>
            <section class="panel detail-panel">
                <h2>Thông tin khách hàng</h2>
                <div class="info-row"><span>Họ tên</span><strong>{{ $order->customer_name }}</strong></div>
                <div class="info-row"><span>Email</span><strong>{{ $order->customer_email }}</strong></div>
                <div class="info-row"><span>Số điện thoại</span><strong>{{ $order->customer_phone }}</strong></div>
                <div class="info-row"><span>Ngày tạo</span><strong>{{ $order->created_at }}</strong></div>
            </section>

            <section class="panel detail-panel">
                <h2>Danh sách tên miền</h2>
                @forelse($items as $item)
                    <div class="order-item">
                        <div class="order-item-head">
                            <strong>{{ $item->domain_name }}</strong>
                            <strong>{{ number_format($item->line_total, 0, ',', '.') }}đ</strong>
                        </div>
                        <p>Đăng ký {{ $item->registration_years }} năm · {{ $item->extension }}</p>
                    </div>
                @empty
                    <p class="muted">Đơn hàng chưa có sản phẩm.</p>
                @endforelse

                <div class="order-total">
                    <strong>Tổng tiền</strong>
                    <strong>{{ number_format($order->total_amount, 0, ',', '.') }}đ</strong>
                </div>
            </section>
        </div>

        <section class="panel detail-panel status-panel">
            <h2>Trạng thái đơn hàng</h2>
            <x-admin.status :status="$order->status" />

            @if(count($nextStatuses) > 0)
                <form method="POST"
                      action="{{ route('admin.orders.update-status', ['id' => $order->id]) }}"
                      class="status-form">
                    @csrf
                    @method('PATCH')

                    <label for="status">Chuyển trạng thái</label>
                    <select id="status" name="status" required>
                        <option value="">-- Chọn trạng thái --</option>
                        @foreach($nextStatuses as $next)
                            <option value="{{ $next }}" @selected(old('status') === $next)>{{ $statusLabels[$next] ?? $next }}</option>
                        @endforeach
                    </select>

                    <label for="status_note">Ghi chú xử lý (không bắt buộc)</label>
                    <textarea id="status_note"
                              name="status_note"
                              rows="3"
                              maxlength="500"
                              placeholder="Ví dụ: Đã kiểm tra thông tin và liên hệ khách hàng">{{ old('status_note') }}</textarea>

                    <button type="submit" class="btn btn-primary">Cập nhật trạng thái</button>
                </form>
            @else
                <p class="muted">Đơn hàng đã kết thúc, không thể chuyển sang trạng thái khác.</p>
            @endif
        </section>
    </div>

    <x-admin.order-status-timeline :order-id="$order->id" />
@endsection
