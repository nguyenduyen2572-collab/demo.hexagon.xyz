<?php

declare(strict_types=1);

namespace Tests\Feature\Hexagon;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\Support\HexagonIsolatedTestCase;

final class AdminOrderFiltersTest extends HexagonIsolatedTestCase
{
    private function makeOrder(string $name, string $email, string $status, string $createdAt): string
    {
        $code = (string) Str::uuid();
        DB::table('orders')->insert([
            'order_code' => $code,
            'customer_name' => $name,
            'customer_email' => $email,
            'customer_phone' => '0900000000',
            'total_amount' => 260000,
            'status' => $status,
            'is_mock' => true,
            'user_id' => null,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);
        return $code;
    }

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    public function test_admin_searches_orders_by_customer_name(): void
    {
        $included = $this->makeOrder('Nguyen Van An', 'an@example.com', 'pending_demo', '2026-11-10 09:30:00');
        $excluded = $this->makeOrder('Tran Thi Binh', 'binh@example.com', 'pending_demo', '2026-11-10 09:30:00');

        $this->actingAs($this->admin())
            ->get(route('admin.orders.index', ['q' => 'Nguyen Van An']))
            ->assertOk()->assertSee($included)->assertDontSee($excluded);
    }

    public function test_admin_filters_by_status(): void
    {
        $included = $this->makeOrder('Khach A', 'a@example.com', 'processing_demo', '2026-11-10 09:30:00');
        $excluded = $this->makeOrder('Khach B', 'b@example.com', 'cancelled_demo', '2026-11-10 09:30:00');

        $this->actingAs($this->admin())
            ->get(route('admin.orders.index', ['status' => 'processing_demo']))
            ->assertOk()->assertSee($included)->assertDontSee($excluded);
    }

    public function test_admin_filters_by_inclusive_date_range(): void
    {
        $included = $this->makeOrder('Khach A', 'a@example.com', 'pending_demo', '2026-11-09 23:59:59');
        $excluded = $this->makeOrder('Khach B', 'b@example.com', 'pending_demo', '2026-11-10 00:00:00');

        $this->actingAs($this->admin())
            ->get(route('admin.orders.index', ['from' => '2026-11-09', 'to' => '2026-11-09']))
            ->assertOk()->assertSee($included)->assertDontSee($excluded);
    }

    public function test_filters_combine_without_altering_order_records(): void
    {
        $included = $this->makeOrder('Duong', 'duong@example.com', 'pending_demo', '2026-11-09 10:00:00');
        $excluded = $this->makeOrder('Duong B', 'duong2@example.com', 'completed_demo', '2026-11-09 10:00:00');

        $this->actingAs($this->admin())
            ->get(route('admin.orders.index', ['q' => 'Duong', 'status' => 'pending_demo']))
            ->assertOk()->assertSee($included)->assertDontSee($excluded);
        $this->assertDatabaseCount('orders', 2);
    }

    public function test_customer_cannot_use_admin_order_list_filters(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => false]))
            ->get(route('admin.orders.index', ['q' => 'test']))
            ->assertForbidden();
    }

    public function test_pagination_preserves_filters(): void
    {
        foreach (range(1, 26) as $n) {
            $this->makeOrder('Khach Loc ' . $n, 'filter'.$n.'@example.com', 'pending_demo', '2026-11-09 10:00:00');
        }

        $this->actingAs($this->admin())
            ->get(route('admin.orders.index', ['status' => 'pending_demo', 'q' => 'Khach Loc']))
            ->assertOk()->assertSee('status=pending_demo', false)->assertSee('q=Khach', false);
    }
}
