@extends('admin.layouts.app')

@section('title', 'Chi tiết đơn hàng - HEXAGON Admin')
@section('topbar_title', 'Chi tiết đơn hàng')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/hexagon-admin-order-detail-v2.css') }}?v=1">
    <link rel="stylesheet" href="{{ asset('css/hexagon-admin-order-quick-v8-4.css') }}?v=1">
    <link rel="stylesheet" href="{{ asset('css/hexagon-admin-assignment-v9-2.css') }}?v=1">
@endpush
@push('scripts')
    <script src="{{ asset('js/hexagon-admin-order-detail-v2.js') }}?v=1" defer></script>
    <script src="{{ asset('js/hexagon-admin-order-quick-v8-4.js') }}?v=1" defer></script>
@endpush

@section('content')
<div class="hx-order-v2">
    <a class="back-button" href="{{ route('admin.orders.index', \App\Support\HexagonAdminOrderNavigation::filters(request())) }}">← Quay lại danh sách đơn hàng</a>

    <div class="page-heading hx-order-v2__heading">
        <div>
            <div class="hx-order-v84__title-row">
                <h1>Đơn hàng {{ \App\Support\HexagonOrderCode::forOrder($order) }}</h1>
                <button type="button" class="hx-order-v84__copy" data-hx-order-copy="{{ \App\Support\HexagonOrderCode::forOrder($order) }}" aria-label="Sao chép mã đơn hàng">Sao chép mã</button>
                <span class="hx-order-v84__copy-status" data-hx-order-copy-status role="status" aria-live="polite"></span>
            </div>
            <p>Được tạo lúc {{ \Illuminate\Support\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}</p>
        </div>
        <x-admin.status :status="$order->status" />
    </div>

    @if(session('success'))
        <div class="alert alert-success" role="status">{{ session('success') }}</div>
    @endif

    @if(session('info'))
        <div class="alert" role="status">{{ session('info') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-error" role="alert">{{ $errors->first() }}</div>
    @endif

    <div class="detail-grid hx-order-v2__grid">
        <div class="hx-order-v2__column">
            <section class="panel detail-panel">
                <h2>Thông tin khách hàng</h2>
                <div class="info-row"><span>Họ tên</span><strong>{{ $order->customer_name }}</strong></div>
                <div class="info-row"><span>Email</span><strong><a class="hx-order-v2__inline-link" href="mailto:{{ $order->customer_email }}">{{ $order->customer_email }}</a></strong></div>
                <div class="info-row"><span>Số điện thoại</span><strong>{{ $order->customer_phone }}</strong></div>
                <div class="info-row"><span>Ngày tạo</span><strong>{{ \Illuminate\Support\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}</strong></div>
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
                    <strong>Tổng giá trị đơn</strong>
                    <strong>{{ number_format($order->total_amount, 0, ',', '.') }}đ</strong>
                </div>
                <p class="hx-order-v2__price-note">Giá trị đơn hàng không xác nhận rằng khách hàng đã thanh toán.</p>
            </section>
        </div>

        <div class="hx-order-v2__column">
            <section class="panel detail-panel hx-order-assignment-panel">
                <h2>Người phụ trách</h2>
                <p class="hx-order-assignment-current">{{ $assignedName ?? 'Chưa phân công' }}</p>
                @if($canAssign && !in_array($order->status, ['completed_demo', 'cancelled_demo'], true))
                    <form method="POST" action="{{ route('admin.orders.assign', array_merge(['id' => $order->id], \App\Support\HexagonAdminOrderNavigation::filters(request()))) }}">
                        @csrf @method('PATCH')
                        <label for="hx-order-employee">Chọn nhân viên xử lý</label>
                        <select id="hx-order-employee" name="assigned_admin_id">
                            <option value="">Chưa phân công</option>
                            @foreach($assignees as $person)
                                <option value="{{ $person->id }}" @selected((int) $order->assigned_admin_id === (int) $person->id)>{{ $person->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary">Lưu phân công</button>
                    </form>
                @endif
                @if($assignmentHistory->isNotEmpty())
                    <details class="hx-order-assignment-history">
                        <summary>Lịch sử phân công ({{ $assignmentHistory->count() }} gần nhất)</summary>
                        @foreach($assignmentHistory as $entry)
                            <p><strong>{{ $entry->actor_name ?? 'Tài khoản cũ' }}</strong>:
                               {{ $entry->previous_name ?? 'Chưa phân công' }} → {{ $entry->next_name ?? 'Chưa phân công' }}
                               <small>{{ \Illuminate\Support\Carbon::parse($entry->created_at)->format('d/m/Y H:i') }}</small>
                            </p>
                        @endforeach
                    </details>
                @endif
            </section>
            <section class="panel detail-panel status-panel">
                <h2>Trạng thái đơn hàng</h2>
                <x-admin.status :status="$order->status" />

                @if(count($nextStatuses) > 0)
                    <form method="POST"
                          action="{{ route('admin.orders.update-status', array_merge(['id' => $order->id], \App\Support\HexagonAdminOrderNavigation::filters(request()))) }}"
                          class="status-form"
                          data-hx-order-status-form>
                        @csrf
                        @method('PATCH')

                        <label for="status">Chuyển trạng thái</label>
                        <select id="status" name="status" required @error('status') aria-invalid="true" @enderror>
                            <option value="">-- Chọn trạng thái --</option>
                            @foreach($nextStatuses as $next)
                                <option value="{{ $next }}" @selected(old('status') === $next)>{{ $statusLabels[$next] ?? $next }}</option>
                            @endforeach
                        </select>
                        @error('status')
                            <p class="hx-order-v2__error">{{ $message }}</p>
                        @enderror

                        <label for="status_note" data-hx-order-note-label>Ghi chú xử lý (không bắt buộc)</label>
                        <textarea id="status_note"
                                  name="status_note"
                                  rows="3"
                                  maxlength="500"
                                  @error('status_note') aria-invalid="true" @enderror
                                  aria-describedby="hx-order-note-hint"
                                  placeholder="Nội dung xử lý, thông tin liên hệ khách hàng...">{{ old('status_note') }}</textarea>
                        <p id="hx-order-note-hint" class="hx-order-v2__hint">Khi hủy đơn hàng, bắt buộc ghi rõ lý do (tối đa 500 ký tự).</p>
                        @error('status_note')
                            <p class="hx-order-v2__error">{{ $message }}</p>
                        @enderror

                        <p class="hx-order-v2__cancel-warning" data-hx-order-cancel-warning hidden>
                            Hủy đơn sẽ kết thúc quy trình xử lý và không thể hoàn tác bằng thao tác đổi trạng thái.
                        </p>
                        <button type="submit" class="btn btn-primary">Cập nhật trạng thái</button>
                    </form>
                @else
                    <p class="hx-order-v2__finished">Đơn hàng đã ở trạng thái cuối, không thể chuyển sang trạng thái khác.</p>
                @endif
            </section>

            <x-admin.order-status-timeline :order-id="(int) $order->id" />
        </div>
    </div>
</div>
@endsection
