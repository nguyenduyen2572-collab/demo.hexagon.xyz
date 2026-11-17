<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Mã đơn hàng công khai, ổn định và dễ tra cứu.
 *
 * UUID trong orders.order_code vẫn là mã nội bộ để giữ tương thích
 * với các liên kết và phiên truy cập cũ. ID tự tăng của MySQL là
 * nguồn duy nhất cho mã HXG; không cần bảng đếm hoặc migration.
 */
final class HexagonOrderCode
{
    public static function fromId(int $id): string
    {
        if ($id < 1) {
            throw new \InvalidArgumentException('ID đơn hàng phải là số nguyên dương.');
        }

        return sprintf('HXG-%02d', $id);
    }

    public static function forOrder(object $order): string
    {
        return self::fromId((int) $order->id);
    }

    public static function toId(string $code): ?int
    {
        $code = strtoupper(trim($code));
        if (!preg_match('/^HXG-([0-9]{2,19})$/', $code, $matches)) {
            return null;
        }

        $digits = ltrim($matches[1], '0');
        $max = (string) PHP_INT_MAX;
        if ($digits === '' || strlen($digits) > strlen($max)
            || (strlen($digits) === strlen($max) && strcmp($digits, $max) > 0)) {
            return null;
        }

        $id = (int) $digits;
        return ($code === self::fromId($id) || $code === sprintf('HXG-%06d', $id))
            ? $id
            : null;
    }
}
