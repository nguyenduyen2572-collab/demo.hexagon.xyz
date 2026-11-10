<?php

declare(strict_types=1);

namespace App\View\Components\Admin;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\Component;

class OrderStatusTimeline extends Component
{
    public Collection $history;

    public function __construct(public int $orderId)
    {
        $this->history = DB::table('order_status_histories as h')
            ->leftJoin('users as u', 'u.id', '=', 'h.changed_by')
            ->where('h.order_id', $orderId)
            ->orderByDesc('h.id')
            ->select('h.from_status', 'h.to_status', 'h.note', 'h.created_at', 'u.name as actor_name')
            ->get();
    }

    public function render(): View
    {
        return view('components.admin.order-status-timeline');
    }
}
