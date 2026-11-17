<?php

namespace Tests\Feature\Hexagon;

use App\Models\User;
use App\Support\HexagonOrderCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\Support\HexagonIsolatedTestCase;

final class AdminOrderActionsCompactTest extends HexagonIsolatedTestCase
{
    private function createOrder(string $status): int
    {
        return (int) DB::table('orders')->insertGetId([
            'order_code' => (string) Str::uuid(),
            'customer_name' => 'Khach hang kiem thu',
            'customer_email' => 'hexagon-order-test@example.test',
            'customer_phone' => '0900000000',
            'total_amount' => 260000,
            'status' => $status,
            'is_mock' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_admin_list_has_a_detail_action_for_each_order(): void
    {
        $pendingId = $this->createOrder('pending_demo');
        $cancelledId = $this->createOrder('cancelled_demo');
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.orders.index'));
        $response->assertOk()
            ->assertSee('Thao tác')
            ->assertSee(HexagonOrderCode::fromId($pendingId))
            ->assertSee(HexagonOrderCode::fromId($cancelledId))
            ->assertSee('Xem & xử lý')
            ->assertSee('Xem chi tiết')
            ->assertSee(route('admin.orders.show', ['id' => $pendingId]), false)
            ->assertSee(route('admin.orders.show', ['id' => $cancelledId]), false);
    }

    public function test_normal_customer_cannot_open_admin_order_detail(): void
    {
        $id = $this->createOrder('pending_demo');
        $customer = User::factory()->create(['is_admin' => false]);
        $this->actingAs($customer)
            ->get(route('admin.orders.show', ['id' => $id]))
            ->assertForbidden();
    }
}
