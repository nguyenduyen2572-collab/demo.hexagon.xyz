@once
<style>
.status-form textarea[name="status_note"] {width:100%;box-sizing:border-box;min-height:84px;padding:11px 13px;border:1px solid #c9d6e8;border-radius:10px;background:#fff;color:#10264c;font:inherit;resize:vertical}
.status-form textarea[name="status_note"]:focus {outline:2px solid #166dec;outline-offset:2px}
.hx-order-history {margin-top:24px;padding:24px 26px;border:1px solid #dce5f1;border-radius:17px;background:#fff;color:#10264c}
.hx-order-history h2 {font-size:20px;font-weight:750;margin:0 0 8px}
.hx-order-history .hx-hint {color:#65758e;font-size:14px;margin:0 0 20px;line-height:1.6}
.hx-history-list {list-style:none;padding:0;margin:0;border-left:2px solid #dbe7fa}
.hx-history-item {position:relative;margin:0 0 20px 20px;padding:0 0 2px 3px}
.hx-history-item:last-child {margin-bottom:0}
.hx-history-item::before {content:"";position:absolute;left:-27px;top:5px;width:11px;height:11px;background:#1870e8;border:3px solid #e7f0ff;border-radius:50%}
.hx-history-head {font-weight:700;line-height:1.5}
.hx-history-meta {display:flex;gap:12px;flex-wrap:wrap;color:#60718d;font-size:13px;margin-top:5px}
.hx-history-note {border-radius:9px;background:#f1f6ff;padding:10px 12px;margin-top:8px;white-space:pre-wrap;overflow-wrap:anywhere;font-size:14px}
@media(max-width:640px){.hx-order-history {padding:20px 16px}}
</style>
@endonce
<section class="hx-order-history" aria-labelledby="hx-order-history-title">
    <h2 id="hx-order-history-title">Lịch sử xử lý đơn hàng</h2>
    <p class="hx-hint">Theo dõi các lần thay đổi trạng thái, người thực hiện và ghi chú xử lý.</p>
    @if($history->isEmpty())
        <p class="hx-hint">Chưa có lần thay đổi trạng thái nào được ghi nhận kể từ khi bật tính năng lịch sử.</p>
    @else
        <ol class="hx-history-list">
            @foreach($history as $event)
                <li class="hx-history-item">
                    <div class="hx-history-head">
                        {{ \App\Support\HexagonOrderStatus::label($event->from_status) }}
                        → {{ \App\Support\HexagonOrderStatus::label($event->to_status) }}
                    </div>
                    <div class="hx-history-meta">
                        <span>{{ $event->actor_name ?: 'Tài khoản quản trị đã xóa' }}</span>
                        <time>{{ \Illuminate\Support\Carbon::parse($event->created_at)->format('d/m/Y H:i') }}</time>
                    </div>
                    @if($event->note)
                        <div class="hx-history-note">{{ $event->note }}</div>
                    @endif
                </li>
            @endforeach
        </ol>
    @endif
</section>
