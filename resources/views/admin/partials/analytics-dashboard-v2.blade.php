{{-- Nạp riêng CSS để không phụ thuộc @stack('styles') trong Layout Admin. --}}
<link rel="stylesheet" href="{{ asset('css/hexagon-admin-analytics-v2.css') }}?v=2.1">

<section class="hx-ana" aria-labelledby="hx-ana-title">
    <header class="hx-ana-heading">
        <div>
            <span class="hx-ana-eyebrow">THỐNG KÊ ĐƠN HÀNG</span>
            <h2 id="hx-ana-title">Phân tích hoạt động</h2>
            <p>Diễn biến số đơn và trạng thái xử lý được tổng hợp trực tiếp từ MySQL.</p>
        </div>
        <form class="hx-ana-period" method="GET" action="{{ route('admin.dashboard') }}" aria-label="Chọn khoảng thống kê">
            <label for="hx-ana-days">Khoảng thời gian</label>
            <select id="hx-ana-days" name="days">
                <option value="7" @selected($hxAnalytics['days'] === 7)>7 ngày</option>
                <option value="14" @selected($hxAnalytics['days'] === 14)>14 ngày</option>
                <option value="30" @selected($hxAnalytics['days'] === 30)>30 ngày</option>
            </select>
            <button type="submit">Áp dụng</button>
        </form>
    </header>

    <div class="hx-ana-kpis">
        <article>
            <span>Đơn hàng trong kỳ</span>
            <strong>{{ number_format($hxAnalytics['total_orders'], 0, ',', '.') }}</strong>
            <small>{{ $hxAnalytics['start'] }} – {{ $hxAnalytics['end'] }}</small>
        </article>
        <article>
            <span>Giá trị đơn chưa hủy</span>
            <strong>{{ number_format($hxAnalytics['active_order_value'], 0, ',', '.') }}<em>đ</em></strong>
            <small>Giá trị các đơn chưa bị hủy</small>
        </article>
    </div>

    <div class="hx-ana-grid">
        <section class="hx-ana-panel hx-ana-activity" aria-labelledby="hx-ana-activity-title">
            <div class="hx-ana-panel-head">
                <div>
                    <h3 id="hx-ana-activity-title">Số đơn theo ngày</h3>
                    <p>{{ $hxAnalytics['start'] }} – {{ $hxAnalytics['end'] }}</p>
                </div>
                <span class="hx-ana-unit">Số đơn</span>
            </div>
            <div class="hx-ana-bars" role="group" aria-label="Biểu đồ số đơn hàng theo từng ngày">
                @foreach($hxAnalytics['daily'] as $day)
                    <div class="hx-ana-bar-col" title="{{ $day['label'] }}: {{ $day['count'] }} đơn">
                        <div class="hx-ana-bar-plot">
                            @if($day['count'] > 0)
                                <span class="hx-ana-bar-num" aria-hidden="true">{{ $day['count'] }}</span>
                            @endif
                            <div class="hx-ana-bar" style="height: {{ $day['height'] }}%"></div>
                        </div>
                        <span class="hx-ana-bar-day">{{ $day['showLabel'] ? $day['label'] : ' ' }}</span>
                        <span class="hx-ana-visually-hidden">Ngày {{ $day['date'] }}: {{ $day['count'] }} đơn</span>
                    </div>
                @endforeach
            </div>
            @if($hxAnalytics['total_orders'] === 0)
                <p class="hx-ana-no-data">Chưa có đơn hàng nào trong khoảng thời gian đã chọn.</p>
            @endif
        </section>

        <section class="hx-ana-panel hx-ana-status" aria-labelledby="hx-ana-status-title">
            <div class="hx-ana-panel-head">
                <div>
                    <h3 id="hx-ana-status-title">Trạng thái đơn hàng</h3>
                    <p>Phân bố trạng thái trong cùng khoảng thời gian</p>
                </div>
            </div>
            <div class="hx-ana-status-list">
                @foreach($hxAnalytics['status_breakdown'] as $row)
                    <div class="hx-ana-status-row">
                        <div class="hx-ana-status-row-head">
                            <span class="hx-ana-status-name"><i class="hx-ana-dot hx-ana-dot-{{ $row['key'] }}" aria-hidden="true"></i>{{ $row['label'] }}</span>
                            <strong>{{ number_format($row['count'], 0, ',', '.') }} <small>({{ number_format($row['percent'], 1, ',', '.') }}%)</small></strong>
                        </div>
                        <div class="hx-ana-progress" role="progressbar" aria-label="{{ $row['label'] }}" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $row['percent'] }}">
                            <span class="hx-ana-progress-fill hx-ana-progress-{{ $row['key'] }}" style="width: {{ $row['percent'] }}%"></span>
                        </div>
                    </div>
                @endforeach
            </div>
            <a class="hx-ana-details-link" href="{{ route('admin.orders.index') }}">Mở danh sách đơn hàng <span aria-hidden="true">→</span></a>
        </section>
    </div>
</section>
