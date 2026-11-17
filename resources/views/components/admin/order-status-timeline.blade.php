<section class="hx-order-history panel" aria-labelledby="hx-order-history-title">
    <div class="hx-order-history__heading">
        <h2 id="hx-order-history-title">Lịch sử xử lý đơn hàng</h2>
        <p class="hx-order-history__intro">Theo dõi người thực hiện, thời gian và ghi chú của các lần thay đổi trạng thái.</p>
    </div>

    @if($history->isEmpty())
        <p class="hx-order-history__empty">Chưa có lịch sử thay đổi trạng thái nào được ghi nhận.</p>
    @else
        <ol class="hx-order-history__list">
            @foreach($history as $event)
                <li class="hx-order-history__item">
                    <div class="hx-order-history__transition">
                        <span>{{ \App\Support\HexagonOrderStatus::label($event->from_status) }}</span>
                        <span class="hx-order-history__arrow" aria-label="chuyển thành">→</span>
                        <strong>{{ \App\Support\HexagonOrderStatus::label($event->to_status) }}</strong>
                    </div>
                    <div class="hx-order-history__meta">
                        <span>{{ $event->actor_name ?: 'Tài khoản quản trị không còn tồn tại' }}</span>
                        <time datetime="{{ \Illuminate\Support\Carbon::parse($event->created_at)->toIso8601String() }}">{{ \Illuminate\Support\Carbon::parse($event->created_at)->format('d/m/Y H:i') }}</time>
                    </div>
                    @if($event->note)
                        <div class="hx-order-history__note">{{ $event->note }}</div>
                    @endif
                </li>
            @endforeach
        </ol>
    @endif
</section>
