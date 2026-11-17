<?php

namespace Tests\Unit;

use App\Support\HexagonOrderCode;
use PHPUnit\Framework\TestCase;

final class HexagonCompactOrderCodeTest extends TestCase
{
    public function test_codes_start_at_two_digits_and_expand_naturally(): void
    {
        foreach ([1 => 'HXG-01', 9 => 'HXG-09', 10 => 'HXG-10', 99 => 'HXG-99',
            100 => 'HXG-100', 1000 => 'HXG-1000'] as $id => $code) {
            $this->assertSame($code, HexagonOrderCode::fromId($id));
            $this->assertSame($id, HexagonOrderCode::toId($code));
        }
    }

    public function test_old_six_digit_links_still_resolve(): void
    {
        $this->assertSame(5, HexagonOrderCode::toId('hxg-000005'));
        $this->assertSame(100, HexagonOrderCode::toId('HXG-000100'));
        $this->assertNull(HexagonOrderCode::toId('HXG-000000'));
    }

    public function test_invalid_or_ambiguous_codes_are_rejected(): void
    {
        foreach (['HXG-1', 'HXG-001', 'HXG-0', 'HXG-00', 'HXG-12x',
            'HXG-9999999999999999999'] as $code) {
            $this->assertNull(HexagonOrderCode::toId($code));
        }
        $this->expectException(\InvalidArgumentException::class);
        HexagonOrderCode::fromId(0);
    }
}
