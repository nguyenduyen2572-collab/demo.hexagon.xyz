<?php

declare(strict_types=1);

namespace Tests\Feature\Hexagon;

use App\Models\User;
use App\Support\HexagonAdminOrderNavigation;
use App\Support\HexagonOrderCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\Support\HexagonIsolatedTestCase;

final class AdminOrderQuickActionsV84Test extends HexagonIsolatedTestCase
{
    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    private function createOrder(string $status = 'pending_demo'): int
    {
        return DB::table('orders')->insertGetId([
            'order_code' => (string) Str::uuid(),
            'customer_name' => 'Khach Hang V84',
            'customer_email' => 'v84@example.com',
            'customer_phone' => '0900000000',
            'total_amount' => 250000,
            'status' => $status,
            'is_mock' => true,
            'user_id' => null,
            'created_at' => '2026-11-10 10:00:00',
            'updated_at' => '2026-11-10 10:00:00',
        ]);
    }

    public function test_filtered_order_list_keeps_context_in_detail_link(): void
    {
        $id = $this->createOrder();
        $params = [
            'q' => 'Khach Hang V84', 'status' => 'pending_demo',
            'from' => '2026-11-01', 'to' => '2026-11-30',
        ];
        $url = route('admin.orders.show', array_merge(['id' => $id], $params));

        $this->actingAs($this->admin(), 'admin')
            ->get(route('admin.orders.index', $params))
            ->assertOk()->assertSee(e($url), false);
    }

    public function test_detail_back_link_preserves_filters_and_shows_copy_button(): void
    {
        $id = $this->createOrder();
        $params = ['q' => 'Khach Hang V84', 'status' => 'pending_demo', 'page' => 2];
        $code = HexagonOrderCode::fromId($id);

        $this->actingAs($this->admin(), 'admin')
            ->get(route('admin.orders.show', array_merge(['id' => $id], $params)))
            ->assertOk()
            ->assertSee(e(route('admin.orders.index', $params)), false)
            ->assertSee('data-hx-order-copy', false)
            ->assertSee(e($code), false);
    }

    public function test_update_status_retains_safe_filters_in_redirect(): void
    {
        $id = $this->createOrder();
        $params = ['q' => 'Khach Hang V84', 'status' => 'pending_demo', 'page' => 2];
        $this->actingAs($this->admin(), 'admin')
            ->patch(route('admin.orders.update-status', array_merge(['id' => $id], $params)), [
                'status' => 'processing_demo', 'status_note' => 'Đã liên hệ',
            ])
            ->assertRedirect(route('admin.orders.show', array_merge(['id' => $id], $params)));
        $this->assertDatabaseHas('orders', ['id' => $id, 'status' => 'processing_demo']);
    }

    public function test_navigation_helper_ignores_invalid_and_unexpected_parameters(): void
    {
        $request = Request::create('/admin/orders/1', 'GET', [
            'q' => ['test'], 'status' => 'admin', 'from' => '2026-99-99',
            'to' => '2026-11-30', 'page' => '0', 'next' => 'https://evil.test',
        ]);
        $this->assertSame(['to' => '2026-11-30'], HexagonAdminOrderNavigation::filters($request));
    }

    public function test_customer_cannot_access_order_detail_with_filters(): void
    {
        $id = $this->createOrder();
        $this->actingAs(User::factory()->create(['is_admin' => false]))
            ->get(route('admin.orders.show', ['id' => $id, 'q' => 'Khach']))
            ->assertForbidden();
    }
}
