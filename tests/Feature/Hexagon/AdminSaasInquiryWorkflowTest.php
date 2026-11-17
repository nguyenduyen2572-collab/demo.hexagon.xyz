<?php

declare(strict_types=1);

namespace Tests\Feature\Hexagon;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\Support\HexagonIsolatedTestCase;

final class AdminSaasInquiryWorkflowTest extends HexagonIsolatedTestCase
{
    private function inquiry(array $changes = []): int
    {
        return (int) DB::table('saas_inquiries')->insertGetId(array_merge([
            'saas_service_id' => null,
            'service_name' => 'Quản lý bán hàng',
            'customer_name' => 'Khách thử nghiệm',
            'customer_email' => 'test@example.com',
            'customer_phone' => '0912345678',
            'message' => 'Cần tư vấn',
            'status' => 'new',
            'admin_note' => null,
            'assigned_admin_id' => null,
            'follow_up_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ], $changes));
    }

    public function test_admin_can_assign_schedule_and_write_durable_history(): void
    {
        $id = $this->inquiry();
        $admin = User::factory()->create(['is_admin' => true, 'name' => 'Admin A']);
        $this->actingAs($admin, 'admin');

        $this->patch(route('admin.saas-inquiries.update', $id), [
            'status' => 'contacted',
            'assigned_admin_id' => $admin->id,
            'follow_up_at' => '2027-01-20T09:30',
            'admin_note' => 'Đã gọi khách hàng',
        ])->assertRedirect(route('admin.saas-inquiries.show', $id))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('saas_inquiries', [
            'id' => $id,
            'status' => 'contacted',
            'assigned_admin_id' => $admin->id,
            'follow_up_at' => '2027-01-20 02:30:00',
            'admin_note' => 'Đã gọi khách hàng',
        ]);
        $this->assertDatabaseHas('saas_inquiry_activities', [
            'saas_inquiry_id' => $id,
            'admin_user_id' => $admin->id,
            'admin_name' => 'Admin A',
            'status_from' => 'new',
            'status_to' => 'contacted',
            'assignee_to' => 'Admin A',
            'note' => 'Đã gọi khách hàng',
        ]);
        $this->get(route('admin.saas-inquiries.show', $id))
            ->assertOk()->assertSee('Lịch sử chăm sóc khách hàng')
            ->assertSee('Đã gọi khách hàng')
            ->assertSee('2027-01-20T09:30', false);
    }

    public function test_new_notes_do_not_overwrite_previous_history(): void
    {
        $id = $this->inquiry();
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin, 'admin');

        $route = route('admin.saas-inquiries.update', $id);
        $this->patch($route, ['status' => 'new', 'admin_note' => 'Liên hệ lần một'])->assertRedirect();
        $this->patch($route, ['status' => 'contacted', 'admin_note' => '<script>alert(1)</script>'])->assertRedirect();
        $this->patch($route, ['status' => 'contacted', 'admin_note' => ''])->assertSessionHas('info');
        $this->assertDatabaseCount('saas_inquiry_activities', 2);
        $this->assertDatabaseHas('saas_inquiries', ['id' => $id, 'admin_note' => '<script>alert(1)</script>']);
        $this->get(route('admin.saas-inquiries.show', $id))
            ->assertOk()->assertSee('Liên hệ lần một')
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_closing_clears_reminder_and_keeps_event_in_history(): void
    {
        $id = $this->inquiry(['status' => 'contacted', 'follow_up_at' => '2027-01-20 00:00:00']);
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin, 'admin');
        $this->patch(route('admin.saas-inquiries.update', $id), ['status' => 'closed'])
            ->assertSessionHas('success');
        $this->assertDatabaseHas('saas_inquiries', ['id' => $id, 'status' => 'closed', 'follow_up_at' => null]);
        $this->assertDatabaseHas('saas_inquiry_activities', [
            'saas_inquiry_id' => $id, 'status_from' => 'contacted', 'status_to' => 'closed',
            'follow_up_from' => '2027-01-20 00:00:00', 'follow_up_to' => null,
        ]);
    }

    public function test_non_admin_cannot_be_assigned_and_cannot_modify_inquiry(): void
    {
        $id = $this->inquiry();
        $staff = User::factory()->create(['is_admin' => true]);
        $customer = User::factory()->create(['is_admin' => false]);
        $route = route('admin.saas-inquiries.update', $id);

        $this->actingAs($staff, 'admin')->patch($route, [
            'status' => 'contacted', 'assigned_admin_id' => $customer->id,
        ])->assertSessionHasErrors('assigned_admin_id');
        $this->assertDatabaseCount('saas_inquiry_activities', 0);
        $this->assertDatabaseHas('saas_inquiries', ['id' => $id, 'status' => 'new']);

        $this->post(route('admin.logout'))->assertRedirect();
        $this->actingAs($customer, 'web')->patch($route, ['status' => 'closed'])->assertForbidden();
        $this->assertDatabaseHas('saas_inquiries', ['id' => $id, 'status' => 'new']);
    }

    public function test_admin_can_filter_overdue_and_own_assigned_inquiries(): void
    {
        $mine = User::factory()->create(['is_admin' => true]);
        $other = User::factory()->create(['is_admin' => true]);
        $overdue = $this->inquiry([
            'customer_name' => 'Khách quá hạn', 'assigned_admin_id' => $mine->id,
            'follow_up_at' => now('UTC')->subDays(2)->format('Y-m-d H:i:s'),
        ]);
        $this->inquiry([
            'customer_name' => 'Khách tương lai', 'assigned_admin_id' => $other->id,
            'follow_up_at' => now('UTC')->addDays(2)->format('Y-m-d H:i:s'),
        ]);
        $this->actingAs($mine, 'admin');
        $this->get(route('admin.saas-inquiries.index', ['due' => 'overdue', 'assigned' => 'mine']))
            ->assertOk()->assertSee('Khách quá hạn')->assertDontSee('Khách tương lai');
        $this->assertSame((int) $overdue, (int) DB::table('saas_inquiries')->where('customer_name', 'Khách quá hạn')->value('id'));
        $this->get(route('admin.dashboard'))->assertOk()->assertSee('Yêu cầu tư vấn SaaS');
    }

    public function test_invalid_date_is_rejected(): void
    {
        $id = $this->inquiry();
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin, 'admin')
            ->patch(route('admin.saas-inquiries.update', $id), [
                'status' => 'contacted', 'follow_up_at' => '2027/01/20 09:30',
            ])->assertSessionHasErrors('follow_up_at');
        $this->assertDatabaseHas('saas_inquiries', ['id' => $id, 'status' => 'new']);
    }
}
