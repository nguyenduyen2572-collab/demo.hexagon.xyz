<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Các trạng thái đơn hàng hiện có của HEXAGON.
 * Không dùng trạng thái thanh toán/đăng ký domain thật trong luồng này.
 */
final class HexagonOrderStatus
{
    private const TRANSITIONS = [
        'pending_demo' => ['processing_demo', 'cancelled_demo'],
        'processing_demo' => ['completed_demo', 'cancelled_demo'],
        'completed_demo' => [],
        'cancelled_demo' => [],
    ];

    private const LABELS = [
        'pending_demo' => 'Chờ xử lý',
        'processing_demo' => 'Đang xử lý',
        'completed_demo' => 'Đã xử lý',
        'cancelled_demo' => 'Đã hủy',
    ];

    public static function canTransition(string $from, string $to): bool
    {
        return in_array($to, self::TRANSITIONS[$from] ?? [], true);
    }

    public static function label(?string $status): string
    {
        return self::LABELS[$status ?? ''] ?? ($status ?? 'Không xác định');
    }

    public static function values(): array
    {
        return array_keys(self::LABELS);
    }
}
