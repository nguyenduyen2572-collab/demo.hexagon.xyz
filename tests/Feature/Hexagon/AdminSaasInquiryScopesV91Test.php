<?php

declare(strict_types=1);

namespace Tests\Feature\Hexagon;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\Support\HexagonIsolatedTestCase;

final class AdminSaasInquiryScopesV91Test extends HexagonIsolatedTestCase
{
    private function account(string $role, array $permissions = ['inquiries'], bool $enabled = true): User
    {
        $user = User::factory()->create(['is_admin' => true]);
        $user->forceFill(['admin_role' => $role, 'admin_permissions' => $permissions, 'admin_enabled' => $enabled])->save();
        return $user;
    }

    private function inquiry(string $name, ?User $assignee = null): int
    {
        return (int) DB::table('saas_inquiries')->insertGetId([
            'saas_service_id' => null,
            'service_name' => 'Dịch vụ SaaS',
            'customer_name' => $name,
            'customer_email' => 'inquiry-'.str_replace(' ', '-', $name).'@hexagon.test',
            'customer_phone' => '0900000000',
            'message' => 'Cần tư vấn', 'status' => 'new', 'admin_note' => null,
            'assigned_admin_id' => $assignee?->id, 'follow_up_at' => null,
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    public function test_staff_sees_only_assigned_requests_and_scoped_counters(): void
    {
        $staff = $this->account('staff');
        $another = $this->account('staff');
        $ownId = $this->inquiry('Khach cua minh', $staff);
        $otherId = $this->inquiry('Khach cua nguoi khac', $another);
        $this->inquiry('Khach chua phan cong');
        $this->actingAs($staff, 'admin')->get(route('admin.saas-inquiries.index'))
            ->assertOk()->assertSee('Khach cua minh')
            ->assertDontSee('Khach cua nguoi khac')
            ->assertDontSee('Khach chua phan cong')
            ->assertSee('Tìm thấy <strong>1</strong>', false)
            ->assertDontSee('name="assigned"', false);
        $this->get(route('admin.saas-inquiries.show', $ownId))->assertOk()
            ->assertDontSee('name="assigned_admin_id"', false);
        $this->get(route('admin.saas-inquiries.show', $otherId))->assertNotFound();
    }

    public function test_staff_cannot_patch_others_or_forge_assignments(): void
    {
        $staff = $this->account('staff');
        $colleague = $this->account('staff');
        $ownId = $this->inquiry('Khach bao ve', $staff);
        $otherId = $this->inquiry('Khach khac', $colleague);
        $this->actingAs($staff, 'admin')->patch(route('admin.saas-inquiries.update', $otherId), [
            'status' => 'contacted', 'admin_note' => 'thay doi trai phep',
        ])->assertNotFound();
        $this->patch(route('admin.saas-inquiries.update', $ownId), [
            'status' => 'contacted', 'assigned_admin_id' => $colleague->id,
        ])->assertForbidden();
        $this->assertDatabaseHas('saas_inquiries', ['id' => $otherId, 'status' => 'new', 'assigned_admin_id' => $colleague->id]);
        $this->assertDatabaseHas('saas_inquiries', ['id' => $ownId, 'status' => 'new', 'assigned_admin_id' => $staff->id]);
    }

    public function test_staff_can_update_assigned_status_note_and_followup(): void
    {
        $staff = $this->account('staff');
        $id = $this->inquiry('Khach duoc giao', $staff);
        $this->actingAs($staff, 'admin')->patch(route('admin.saas-inquiries.update', $id), [
            'status' => 'contacted', 'admin_note' => 'Da lien he',
            'follow_up_at' => '2026-12-01T10:15',
        ])->assertRedirect(route('admin.saas-inquiries.show', $id));
        $this->assertDatabaseHas('saas_inquiries', ['id' => $id, 'status' => 'contacted', 'assigned_admin_id' => $staff->id]);
        $this->assertDatabaseHas('saas_inquiry_activities', ['saas_inquiry_id' => $id, 'admin_user_id' => $staff->id]);
    }

    public function test_manager_assigns_only_authorized_active_accounts(): void
    {
        $manager = $this->account('manager');
        $worker = $this->account('staff');
        $noAccess = $this->account('staff', ['saas']);
        $disabled = $this->account('staff', ['inquiries'], false);
        $id = $this->inquiry('Khach can phan cong');
        $this->actingAs($manager, 'admin')->get(route('admin.saas-inquiries.show', $id))
            ->assertOk()->assertSee($worker->name);
        foreach ([$noAccess, $disabled] as $target) {
            $this->patch(route('admin.saas-inquiries.update', $id), [
                'status' => 'new', 'assigned_admin_id' => $target->id,
            ])->assertSessionHasErrors('assigned_admin_id');
        }
        $this->patch(route('admin.saas-inquiries.update', $id), [
            'status' => 'new', 'assigned_admin_id' => $worker->id,
        ])->assertRedirect(route('admin.saas-inquiries.show', $id));
        $this->assertDatabaseHas('saas_inquiries', ['id' => $id, 'assigned_admin_id' => $worker->id]);
    }

    public function test_staff_without_inquiry_permission_cannot_access_route(): void
    {
        $staff = $this->account('staff', ['saas']);
        $this->actingAs($staff, 'admin')->get(route('admin.saas-inquiries.index'))->assertForbidden();
    }

    public function test_super_admin_can_manage_unassigned_requests(): void
    {
        $super = $this->account('super_admin', []);
        $staff = $this->account('staff');
        $id = $this->inquiry('Khach cho phan cong');
        $this->actingAs($super, 'admin')->patch(route('admin.saas-inquiries.update', $id), [
            'status' => 'new', 'assigned_admin_id' => $staff->id,
        ])->assertRedirect(route('admin.saas-inquiries.show', $id));
        $this->assertDatabaseHas('saas_inquiries', ['id' => $id, 'assigned_admin_id' => $staff->id]);
    }
}
