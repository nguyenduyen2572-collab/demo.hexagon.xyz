<?php

declare(strict_types=1);

namespace App\Support;

use DateTimeImmutable;
use Illuminate\Http\Request;

/**
 * Chỉ giữ các tham số lọc an toàn khi điều hướng từ danh sách đơn hàng
 * đến trang chi tiết, sau khi cập nhật trạng thái và khi quay lại.
 */
final class HexagonAdminOrderNavigation
{
    private const STATUSES = ['pending_demo', 'processing_demo', 'completed_demo', 'cancelled_demo'];

    public static function filters(Request $request): array
    {
        $query = [];
        $keyword = $request->query('q');
        if (is_string($keyword)) {
            $keyword = trim($keyword);
            $keyword = function_exists('mb_substr') ? mb_substr($keyword, 0, 120) : substr($keyword, 0, 120);
            if ($keyword !== '') {
                $query['q'] = $keyword;
            }
        }

        $status = $request->query('status');
        if (is_string($status) && in_array($status, self::STATUSES, true)) {
            $query['status'] = $status;
        }

        foreach (['from', 'to'] as $name) {
            $value = $request->query($name);
            if (!is_string($value)) {
                continue;
            }
            $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
            if ($date !== false && $date->format('Y-m-d') === $value) {
                $query[$name] = $value;
            }
        }

        $page = $request->query('page');
        if (is_string($page) && preg_match('/^[1-9][0-9]{0,5}$/D', $page)) {
            $query['page'] = (int) $page;
        }

        return $query;
    }
}
