<?php

declare(strict_types=1);

namespace Tests\Feature\Hexagon;

use App\Models\User;
use App\Support\HexagonDashboardAnalytics;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\Support\HexagonIsolatedTestCase;

final class AdminDashboardAnalyticsTest extends HexagonIsolatedTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow('2026-11-10 12:00:00');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function makeOrder(string $status, string $createdAt, int $amount = 260000): void
    {
        DB::table('orders')->insert([
            'order_code' => (string) Str::uuid(),
            'customer_name' => 'Khach test analytics',
            'customer_email' => 'analytics@example.com',
            'customer_phone' => '0900000000',
            'total_amount' => $amount,
            'status' => $status,
            'is_mock' => true,
            'user_id' => null,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);
    }

    public function test_dashboard_only_available_to_admin(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => false]))
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_dashboard_displays_new_analytics_and_respects_selected_period(): void
    {
        $this->makeOrder('pending_demo', '2026-11-10 10:30:00');

        $this->actingAs(User::factory()->create(['is_admin' => true]))
            ->get(route('admin.dashboard', ['days' => 7]))
            ->assertOk()
            ->assertSee('Phân tích hoạt động')
            ->assertSee('Giá trị đơn chưa hủy')
            ->assertSee('Số đơn theo ngày')
            ->assertSee('04/11/2026')
            ->assertSee('10/11/2026');
    }

    public function test_status_summary_excludes_cancelled_value_but_counts_cancelled_orders(): void
    {
        $this->makeOrder('pending_demo', '2026-11-10 10:30:00', 260000);
        $this->makeOrder('cancelled_demo', '2026-11-10 11:00:00', 900000);
        $summary = app(HexagonDashboardAnalytics::class)->forDays(7);

        $this->assertSame(2, $summary['total_orders']);
        $this->assertSame(260000.0, $summary['active_order_value']);
        $counts = array_column($summary['status_breakdown'], 'count', 'key');
        $this->assertSame(1, $counts['pending_demo']);
        $this->assertSame(1, $counts['cancelled_demo']);
    }

    public function test_orders_outside_period_do_not_appear_in_summary(): void
    {
        $this->makeOrder('pending_demo', '2026-11-01 10:30:00');
        $this->makeOrder('processing_demo', '2026-11-10 10:30:00');

        $summary = app(HexagonDashboardAnalytics::class)->forDays(7);
        $this->assertSame(1, $summary['total_orders']);
        $this->assertSame(7, count($summary['daily']));
    }

    public function test_invalid_and_array_period_values_fall_back_to_14_days(): void
    {
        $this->assertSame(14, app(HexagonDashboardAnalytics::class)->forDays('365')['days']);
        $this->assertSame(14, app(HexagonDashboardAnalytics::class)->forDays(['7'])['days']);
        $this->assertSame(30, app(HexagonDashboardAnalytics::class)->forDays('30')['days']);
    }

    public function test_empty_dataset_still_returns_all_days_and_statuses(): void
    {
        $summary = app(HexagonDashboardAnalytics::class)->forDays(14);
        $this->assertSame(0, $summary['total_orders']);
        $this->assertCount(14, $summary['daily']);
        $this->assertCount(4, $summary['status_breakdown']);
    }
}
