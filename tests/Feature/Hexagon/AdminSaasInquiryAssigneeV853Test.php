<?php

declare(strict_types=1);

namespace Tests\Feature\Hexagon;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\Support\HexagonIsolatedTestCase;

final class AdminSaasInquiryAssigneeV853Test extends HexagonIsolatedTestCase
{
    private function inquiry(string $name): int
    {
        return (int) DB::table('saas_inquiries')->insertGetId([
            'saas_service_id' => null,
            'service_name' => 'Tư vấn SaaS',
            'customer_name' => $name,
            'customer_email' => strtolower(str_replace(' ', '-', $name)).'@example.com',
            'customer_phone' => '0900000000',
            'message' => 'Cần tư vấn',
            'status' => 'new',
            'admin_note' => null,
            'assigned_admin_id' => null,
            'follow_up_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_filter_lists_admin_names_and_excludes_customer_accounts(): void
    {
        $viewer = User::factory()->create(['name' => 'Admin kiểm tra', 'is_admin' => true]);
        $employee = User::factory()->create(['name' => 'Nhân viên Nguyễn Minh', 'is_admin' => true]);
        $customer = User::factory()->create(['name' => 'Khách không phải Admin', 'is_admin' => false]);
        $this->actingAs($viewer, 'admin')
            ->get(route('admin.saas-inquiries.index'))
            ->assertOk()
            ->assertSee('value="'.$employee->id.'"', false)
            ->assertSee('Nhân viên Nguyễn Minh')
            ->assertDontSee('Khách không phải Admin')
            ->assertDontSee('<select name="sort">', false);

        $this->get(route('admin.saas-inquiries.index', ['assigned' => $customer->id]))
            ->assertSessionHasErrors('assigned');
    }

    public function test_assigning_an_admin_updates_list_and_filter_with_safe_return_url(): void
    {
        $viewer = User::factory()->create(['is_admin' => true]);
        $employee = User::factory()->create(['name' => 'Phụ trách Trần An', 'is_admin' => true]);
        $otherEmployee = User::factory()->create(['name' => 'Phụ trách Trần Bình', 'is_admin' => true]);
        $assignedId = $this->inquiry('Khách được giao');
        $otherId = $this->inquiry('Khách chưa được giao');
        $this->actingAs($viewer, 'admin');
        $this->patch(route('admin.saas-inquiries.update', $assignedId), [
            'status' => 'contacted',
            'assigned_admin_id' => $employee->id,
            'admin_note' => 'Đã phân công',
        ])->assertRedirect(route('admin.saas-inquiries.show', $assignedId));
        $this->assertDatabaseHas('saas_inquiries', [
            'id' => $assignedId,
            'assigned_admin_id' => $employee->id,
        ]);
        $this->assertDatabaseHas('saas_inquiry_activities', [
            'saas_inquiry_id' => $assignedId,
            'assignee_to' => 'Phụ trách Trần An',
        ]);
        $filters = ['assigned' => (string) $employee->id];
        $show = route('admin.saas-inquiries.show', ['id' => $assignedId] + $filters);
        $this->get(route('admin.saas-inquiries.index', $filters))
            ->assertOk()->assertSee('Khách được giao')->assertDontSee('Khách chưa được giao');
        $this->get($show)->assertOk()->assertSee(e(route('admin.saas-inquiries.index', $filters)), false);
        $this->patch(route('admin.saas-inquiries.update', ['id' => $assignedId] + $filters), [
            'status' => 'contacted', 'assigned_admin_id' => $otherEmployee->id,
        ])->assertRedirect($show);
        $this->assertDatabaseHas('saas_inquiries', ['id' => $assignedId, 'assigned_admin_id' => $otherEmployee->id]);
        $this->get(route('admin.saas-inquiries.index', $filters))->assertDontSee('Khách được giao');
        $this->get(route('admin.saas-inquiries.index', ['assigned' => (string) $otherEmployee->id]))
            ->assertSee('Khách được giao');
    }

    public function test_cannot_assign_request_to_a_non_admin_user(): void
    {
        $viewer = User::factory()->create(['is_admin' => true]);
        $customer = User::factory()->create(['is_admin' => false]);
        $id = $this->inquiry('Khách cần tư vấn');
        $this->actingAs($viewer, 'admin')->patch(route('admin.saas-inquiries.update', $id), [
            'status' => 'contacted', 'assigned_admin_id' => $customer->id,
        ])->assertSessionHasErrors('assigned_admin_id');
        $this->assertDatabaseHas('saas_inquiries', ['id' => $id, 'assigned_admin_id' => null, 'status' => 'new']);
    }
}
