<?php

declare(strict_types=1);

namespace App\Support;

use DateTimeImmutable;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;

/**
 * Bộ lọc danh sách đơn hàng Admin. Chỉ sử dụng Query Builder với tham số ràng buộc.
 * Không can thiệp Controller xử lý trạng thái hoặc Checkout.
 */
final class HexagonAdminOrderFilters
{
    private const STATUSES = [
        'pending_demo',
        'processing_demo',
        'completed_demo',
        'cancelled_demo',
    ];

    public static function apply(Builder $query, Request $request): Builder
    {
        $keyword = self::scalar($request->query('q'));
        if ($keyword !== '') {
            // Giới hạn độ dài truy vấn, tránh từ khóa quá lớn gây tốn tài nguyên.
            $keyword = function_exists('mb_substr')
                ? mb_substr($keyword, 0, 120)
                : substr($keyword, 0, 120);
            // Không để % và _ do người dùng nhập trở thành ký tự đại diện LIKE.
            $escaped = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $keyword);
            $pattern = '%' . $escaped . '%';
            $query->where(static function (Builder $nested) use ($pattern): void {
                $nested->where('order_code', 'like', $pattern)
                    ->orWhere('customer_name', 'like', $pattern)
                    ->orWhere('customer_email', 'like', $pattern)
                    ->orWhere('customer_phone', 'like', $pattern);
            });
        }

        $status = self::scalar($request->query('status'));
        if (in_array($status, self::STATUSES, true)) {
            $query->where('status', $status);
        }

        $from = self::date(self::scalar($request->query('from')));
        $to = self::date(self::scalar($request->query('to')));
        if ($from !== null && $to !== null && $from > $to) {
            // Khoảng ngày ngược: không hiển thị dữ liệu không liên quan.
            return $query->whereRaw('1 = 0');
        }
        if ($from !== null) {
            $query->where('created_at', '>=', $from . ' 00:00:00');
        }
        if ($to !== null) {
            $next = (new DateTimeImmutable($to))->modify('+1 day')->format('Y-m-d');
            $query->where('created_at', '<', $next . ' 00:00:00');
        }

        return $query;
    }

    private static function scalar(mixed $value): string
    {
        return is_string($value) ? trim($value) : '';
    }

    private static function date(string $value): ?string
    {
        if ($value === '') {
            return null;
        }
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
        return $date && $date->format('Y-m-d') === $value ? $value : null;
    }
}
