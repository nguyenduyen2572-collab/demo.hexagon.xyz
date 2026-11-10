<link rel="stylesheet" href="{{ asset('css/hexagon-admin-order-filters-v1.css') }}">

<section class="hx-order-filter" aria-label="Tìm kiếm và lọc đơn hàng">
    <form method="GET" action="{{ route('admin.orders.index') }}" class="hx-order-filter__form">
        <div class="hx-order-filter__field hx-order-filter__field--search">
            <label for="hx-order-q">Tìm kiếm đơn hàng</label>
            <input id="hx-order-q" name="q" type="search" maxlength="120"
                placeholder="Mã đơn, khách hàng, email, số điện thoại"
                value="{{ is_string(request()->query('q')) ? request()->query('q') : '' }}">
        </div>
        <div class="hx-order-filter__field">
            <label for="hx-order-status">Trạng thái</label>
            <select id="hx-order-status" name="status">
                <option value="">Tất cả trạng thái</option>
                <option value="pending_demo" @selected(request('status') === 'pending_demo')>Chờ xử lý</option>
                <option value="processing_demo" @selected(request('status') === 'processing_demo')>Đang xử lý</option>
                <option value="completed_demo" @selected(request('status') === 'completed_demo')>Đã xử lý</option>
                <option value="cancelled_demo" @selected(request('status') === 'cancelled_demo')>Đã hủy</option>
            </select>
        </div>
        <div class="hx-order-filter__field">
            <label for="hx-order-from">Từ ngày</label>
            <input id="hx-order-from" name="from" type="date"
                value="{{ is_string(request()->query('from')) ? request()->query('from') : '' }}">
        </div>
        <div class="hx-order-filter__field">
            <label for="hx-order-to">Đến ngày</label>
            <input id="hx-order-to" name="to" type="date"
                value="{{ is_string(request()->query('to')) ? request()->query('to') : '' }}">
        </div>
        <div class="hx-order-filter__actions">
            <button type="submit" class="hx-order-filter__submit">Áp dụng bộ lọc</button>
            <a href="{{ route('admin.orders.index') }}" class="hx-order-filter__reset">Xóa bộ lọc</a>
        </div>
    </form>
    <div class="hx-order-filter__meta" aria-live="polite">
        <strong>{{ number_format($orders->total(), 0, ',', '.') }}</strong>
        <span>đơn hàng phù hợp</span>
        @if(request()->filled('from') && request()->filled('to') && request('from') > request('to'))
            <span class="hx-order-filter__warning">Khoảng ngày không hợp lệ: ngày bắt đầu phải trước hoặc bằng ngày kết thúc.</span>
        @endif
    </div>
</section>
