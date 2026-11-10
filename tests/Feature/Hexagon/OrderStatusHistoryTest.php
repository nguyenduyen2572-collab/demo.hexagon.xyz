<?php

declare(strict_types=1);

namespace Tests\Feature\Hexagon;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\Support\HexagonIsolatedTestCase;

class OrderStatusHistoryTest extends HexagonIsolatedTestCase
{
    private function createOrder(string $status = 'pending_demo'): int
    {
        return DB::table('orders')->insertGetId([
            'order_code' => (string) Str::uuid(),
            'customer_name' => 'Nguyen Van An',
            'customer_email' => 'buyer@gmail.com',
            'customer_phone' => '0900000000',
            'total_amount' => 260000,
            'status' => $status,
            'is_mock' => true,
            'user_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    public function test_admin_can_move_pending_to_processing_and_history_is_recorded(): void
    {
        $orderId = $this->createOrder();
        $admin = $this->admin();

        $this->actingAs($admin)
            ->patch(route('admin.orders.update-status', ['id' => $orderId]), [
                'status' => 'processing_demo',
                'status_note' => 'Da lien he khach hang',
            ])->assertRedirect();

        $this->assertDatabaseHas('orders', ['id' => $orderId, 'status' => 'processing_demo']);
        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $orderId,
            'changed_by' => $admin->id,
            'from_status' => 'pending_demo',
            'to_status' => 'processing_demo',
            'note' => 'Da lien he khach hang',
        ]);
    }

    public function test_admin_can_cancel_pending_order(): void
    {
        $orderId = $this->createOrder();

        $this->actingAs($this->admin())
            ->patch(route('admin.orders.update-status', ['id' => $orderId]), [
                'status' => 'cancelled_demo',
            ])->assertRedirect();

        $this->assertDatabaseHas('orders', ['id' => $orderId, 'status' => 'cancelled_demo']);
        $this->assertDatabaseCount('order_status_histories', 1);
    }

    public function test_cannot_skip_processing_step(): void
    {
        $orderId = $this->createOrder();

        $this->actingAs($this->admin())
            ->from(route('admin.orders.show', ['id' => $orderId]))
            ->patch(route('admin.orders.update-status', ['id' => $orderId]), [
                'status' => 'completed_demo',
            ])->assertRedirect()->assertSessionHasErrors('status');

        $this->assertDatabaseHas('orders', ['id' => $orderId, 'status' => 'pending_demo']);
        $this->assertDatabaseCount('order_status_histories', 0);
    }

    public function test_finished_order_cannot_change_status_again(): void
    {
        $orderId = $this->createOrder('completed_demo');

        $this->actingAs($this->admin())
            ->patch(route('admin.orders.update-status', ['id' => $orderId]), [
                'status' => 'cancelled_demo',
            ])->assertSessionHasErrors('status');

        $this->assertDatabaseHas('orders', ['id' => $orderId, 'status' => 'completed_demo']);
        $this->assertDatabaseCount('order_status_histories', 0);
    }

    public function test_non_admin_cannot_change_status(): void
    {
        $orderId = $this->createOrder();

        $this->actingAs(User::factory()->create(['is_admin' => false]))
            ->patch(route('admin.orders.update-status', ['id' => $orderId]), [
                'status' => 'processing_demo',
            ])->assertForbidden();

        $this->assertDatabaseHas('orders', ['id' => $orderId, 'status' => 'pending_demo']);
        $this->assertDatabaseCount('order_status_histories', 0);
    }

    public function test_status_history_is_visible_in_admin_order_details(): void
    {
        $orderId = $this->createOrder();
        DB::table('order_status_histories')->insert([
            'order_id' => $orderId,
            'changed_by' => $this->admin()->id,
            'from_status' => 'pending_demo',
            'to_status' => 'processing_demo',
            'note' => 'Ghi chu xu ly thu nghiem',
            'created_at' => now(),
        ]);

        $this->actingAs($this->admin())
            ->get(route('admin.orders.show', ['id' => $orderId]))
            ->assertOk()
            ->assertSee('Lịch sử xử lý đơn hàng')
            ->assertSee('Ghi chu xu ly thu nghiem');
    }

    public function test_rejects_note_over_500_characters(): void
    {
        $orderId = $this->createOrder();

        $this->actingAs($this->admin())
            ->patch(route('admin.orders.update-status', ['id' => $orderId]), [
                'status' => 'processing_demo',
                'status_note' => str_repeat('x', 501),
            ])->assertSessionHasErrors('status_note');

        $this->assertDatabaseCount('order_status_histories', 0);
    }
}
