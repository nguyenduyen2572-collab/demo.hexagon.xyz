<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Các số liệu trên Dashboard là đơn hàng demo, tuyệt đối không coi là tiền đã thu.
 * Hai truy vấn tổng hợp, không đọc từng đơn vào PHP.
 */
final class HexagonDashboardAnalytics
{
    private const STATUS_LABELS = [
        'pending_demo' => 'Chờ xử lý',
        'processing_demo' => 'Đang xử lý',
        'completed_demo' => 'Đã xử lý',
        'cancelled_demo' => 'Đã hủy',
    ];

    /** @return array<string, mixed> */
    public function forDays(mixed $requestedDays = null): array
    {
        $requested = is_int($requestedDays) || is_string($requestedDays)
            ? (string) $requestedDays
            : '14';
        $days = in_array($requested, ['7', '14', '30'], true) ? (int) $requested : 14;

        $today = Carbon::today();
        $start = $today->copy()->subDays($days - 1)->startOfDay();
        $endExclusive = $today->copy()->addDay()->startOfDay();

        // Đếm theo trạng thái, bao gồm cả những trạng thái mới chưa có tên trong hệ thống.
        $statusRows = DB::table('orders')
            ->where('created_at', '>=', $start)
            ->where('created_at', '<', $endExclusive)
            ->select('status')
            ->selectRaw('COUNT(*) AS order_count, COALESCE(SUM(total_amount), 0) AS order_value')
            ->groupBy('status')
            ->get();

        $statusCounts = array_fill_keys(array_keys(self::STATUS_LABELS), 0);
        $totalOrders = 0;
        $activeOrderValue = 0.0;

        foreach ($statusRows as $row) {
            $status = (string) $row->status;
            $count = (int) $row->order_count;
            $totalOrders += $count;
            if (array_key_exists($status, $statusCounts)) {
                $statusCounts[$status] += $count;
            } else {
                $statusCounts['other'] = ($statusCounts['other'] ?? 0) + $count;
            }
            if ($status !== 'cancelled_demo') {
                $activeOrderValue += (float) $row->order_value;
            }
        }

        $statusBreakdown = [];
        foreach ($statusCounts as $key => $count) {
            $statusBreakdown[] = [
                'key' => $key,
                'label' => self::STATUS_LABELS[$key] ?? 'Trạng thái khác',
                'count' => $count,
                'percent' => $totalOrders > 0 ? round($count / $totalOrders * 100, 1) : 0,
            ];
        }

        // DATE() được hỗ trợ bởi MySQL và SQLite. Chỉ truy vấn đúng khoảng đang hiển thị.
        $dailyRows = DB::table('orders')
            ->where('created_at', '>=', $start)
            ->where('created_at', '<', $endExclusive)
            ->selectRaw('DATE(created_at) AS order_day, COUNT(*) AS order_count')
            ->groupByRaw('DATE(created_at)')
            ->orderBy('order_day')
            ->get();
        $countsByDate = [];
        foreach ($dailyRows as $row) {
            $countsByDate[(string) $row->order_day] = (int) $row->order_count;
        }
        $maxDaily = max(1, ...array_values($countsByDate ?: [0]));

        $daily = [];
        for ($offset = 0; $offset < $days; $offset++) {
            $date = $start->copy()->addDays($offset);
            $key = $date->format('Y-m-d');
            $count = $countsByDate[$key] ?? 0;
            $daily[] = [
                'date' => $key,
                'label' => $date->format('d/m'),
                'count' => $count,
                'height' => $count === 0 ? 0 : max(7, (int) round($count / $maxDaily * 100)),
                'showLabel' => $offset === 0 || $offset === $days - 1 || $offset % ($days === 30 ? 5 : ($days === 14 ? 2 : 1)) === 0,
            ];
        }

        return [
            'days' => $days,
            'start' => $start->format('d/m/Y'),
            'end' => $today->format('d/m/Y'),
            'total_orders' => $totalOrders,
            'active_order_value' => $activeOrderValue,
            'status_breakdown' => $statusBreakdown,
            'daily' => $daily,
        ];
    }
}
