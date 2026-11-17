<?php

declare(strict_types=1);

namespace Tests\Feature\Hexagon;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\Support\HexagonIsolatedTestCase;

final class AdminDashboardCompletedStatsTest extends HexagonIsolatedTestCase
{
    private function createOrder(string $status, int $amount): void
    {
        DB::table('orders')->insert([
            'order_code' => (string) Str::uuid(),
            'customer_name' => 'Khách kiểm thử',
            'customer_email' => (string) Str::uuid() . '@example.com',
            'customer_phone' => '0900000000',
            'total_amount' => $amount,
            'status' => $status,
            'is_mock' => true,
            'user_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_completed_counter_excludes_pending_processing_and_cancelled_orders(): void
    {
        $this->createOrder('pending_demo', 100000);
        $this->createOrder('processing_demo', 200000);
        $this->createOrder('completed_demo', 300000);
        $this->createOrder('cancelled_demo', 900000);

        $response = $this->actingAs(User::factory()->create(['is_admin'=>true]), 'admin')
            ->get(route('admin.dashboard'));

        $response->assertOk()->assertSee('Đơn đã hoàn tất');
        $this->assertMatchesRegularExpression(
            '~class="stat-label">Đơn đã hoàn tất</div>\s*<div class="stat-value">1</div>~u',
            $response->getContent()
        );
    }

    public function test_completed_counter_shows_zero_when_no_completed_orders(): void
    {
        $this->createOrder('cancelled_demo', 999000);
        $response = $this->actingAs(User::factory()->create(['is_admin'=>true]), 'admin')
            ->get(route('admin.dashboard'));

        $response->assertOk();
        $this->assertMatchesRegularExpression(
            '~class="stat-label">Đơn đã hoàn tất</div>\s*<div class="stat-value">0</div>~u',
            $response->getContent()
        );
    }
}
