<?php

declare(strict_types=1);

namespace Tests\Feature\Hexagon;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\Support\HexagonIsolatedTestCase;

final class AdminOrderAssignmentsV92Test extends HexagonIsolatedTestCase
{
    private function account(string $role, array $permissions = ['orders'], bool $enabled = true): User
    {
        $user = User::factory()->create(['is_admin' => true]);
        $user->forceFill(['admin_role' => $role, 'admin_permissions' => $permissions, 'admin_enabled' => $enabled])->save();
        return $user;
    }

    private function order(string $name = 'Đơn chưa phân công', ?User $employee = null): int
    {
        return (int) DB::table('orders')->insertGetId([
            'order_code' => (string) Str::uuid(), 'customer_name' => $name,
            'customer_email' => Str::slug($name).'@hexagon.test', 'customer_phone' => '0900000000',
            'total_amount' => 220000, 'status' => 'pending_demo', 'is_mock' => true,
            'user_id' => null, 'assigned_admin_id' => $employee?->id,
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    public function test_super_admin_can_assign_and_history_is_recorded(): void
    {
        $super = $this->account('super_admin', []);
        $staff = $this->account('staff');
        $id = $this->order();
        $this->actingAs($super, 'admin')->patch(route('admin.orders.assign', $id), [
            'assigned_admin_id' => $staff->id,
        ])->assertRedirect(route('admin.orders.show', $id));
        $this->assertDatabaseHas('orders', ['id' => $id, 'assigned_admin_id' => $staff->id]);
        $this->assertDatabaseHas('order_assignment_histories', [
            'order_id' => $id, 'changed_by' => $super->id,
            'from_admin_id' => null, 'to_admin_id' => $staff->id,
        ]);
    }

    public function test_manager_assigns_only_to_active_authorized_staff(): void
    {
        $manager = $this->account('manager');
        $staff = $this->account('staff');
        $disabled = $this->account('staff', ['orders'], false);
        $unrelated = $this->account('staff', ['saas']);
        $id = $this->order();
        $this->actingAs($manager, 'admin');
        foreach ([$disabled, $unrelated] as $worker) {
            $this->patch(route('admin.orders.assign', $id), [
                'assigned_admin_id' => $worker->id,
            ])->assertSessionHasErrors('assigned_admin_id');
        }
        $this->patch(route('admin.orders.assign', $id), [
            'assigned_admin_id' => $staff->id,
        ])->assertRedirect(route('admin.orders.show', $id));
        $this->assertDatabaseHas('orders', ['id' => $id, 'assigned_admin_id' => $staff->id]);
    }

    public function test_staff_sees_only_own_orders_and_cannot_reassign(): void
    {
        $worker = $this->account('staff');
        $other = $this->account('staff');
        $own = $this->order('Đơn của nhân viên một', $worker);
        $different = $this->order('Đơn của nhân viên hai', $other);
        $unassigned = $this->order('Đơn chưa nhận');
        $this->actingAs($worker, 'admin')->get(route('admin.orders.index'))
            ->assertOk()->assertSee('Đơn của nhân viên một')
            ->assertDontSee('Đơn của nhân viên hai')->assertDontSee('Đơn chưa nhận');
        $this->get(route('admin.orders.show', $own))->assertOk()
            ->assertDontSee('name="assigned_admin_id"', false);
        $this->get(route('admin.orders.show', $different))->assertNotFound();
        $this->get(route('admin.orders.show', $unassigned))->assertNotFound();
        $this->patch(route('admin.orders.assign', $own), ['assigned_admin_id' => $other->id])
            ->assertForbidden();
        $this->patch(route('admin.orders.update-status', $different), [
            'status' => 'processing_demo', 'status_note' => 'Không có quyền',
        ])->assertNotFound();
        $this->assertDatabaseHas('orders', ['id' => $different, 'status' => 'pending_demo']);
    }

    public function test_staff_updates_assigned_order_only(): void
    {
        $staff = $this->account('staff');
        $own = $this->order('Đơn cần xử lý', $staff);
        $this->actingAs($staff, 'admin')->patch(route('admin.orders.update-status', $own), [
            'status' => 'processing_demo', 'status_note' => 'Đã liên hệ khách',
        ])->assertRedirect(route('admin.orders.show', $own));
        $this->assertDatabaseHas('orders', ['id' => $own, 'status' => 'processing_demo']);
        $this->assertDatabaseHas('order_status_histories', ['order_id' => $own, 'changed_by' => $staff->id]);
    }

    public function test_staff_without_order_permission_is_forbidden(): void
    {
        $staff = $this->account('staff', ['saas']);
        $this->actingAs($staff, 'admin')->get(route('admin.orders.index'))->assertForbidden();
    }
}
