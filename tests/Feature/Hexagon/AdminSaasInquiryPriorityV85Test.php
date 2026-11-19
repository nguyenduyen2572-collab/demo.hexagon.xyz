<?php

declare(strict_types=1);

namespace Tests\Feature\Hexagon;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\Support\HexagonIsolatedTestCase;

final class AdminSaasInquiryPriorityV85Test extends HexagonIsolatedTestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function makeInquiry(string $customer, string $status, ?string $followUp = null): int
    {
        return (int) DB::table('saas_inquiries')->insertGetId([
            'saas_service_id' => null,
            'service_name' => 'Dịch vụ SaaS',
            'customer_name' => $customer,
            'customer_email' => 'test-'.str_replace(' ', '-', $customer).'@example.com',
            'customer_phone' => '0900000000',
            'message' => 'Tư vấn',
            'status' => $status,
            'admin_note' => null,
            'assigned_admin_id' => null,
            'follow_up_at' => $followUp,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    public function test_priority_order_places_overdue_before_new_and_completed_last(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-19 08:00:00', 'Asia/Ho_Chi_Minh'));
        $this->makeInquiry('Khach hoan tat', 'closed');
        $this->makeInquiry('Khach lich sap toi', 'contacted', '2026-11-20 02:00:00');
        $this->makeInquiry('Khach moi', 'new');
        $this->makeInquiry('Khach qua han', 'contacted', '2026-11-18 02:00:00');

        $this->actingAs($this->admin(), 'admin')
            ->get(route('admin.saas-inquiries.index'))
            ->assertOk()
            ->assertSeeInOrder(['Khach qua han', 'Khach moi', 'Khach lich sap toi', 'Khach hoan tat'])
            ->assertSee('Quá hạn — cần liên hệ')
            ->assertSee('Trong 7 ngày tới');
    }

    public function test_next_seven_days_filter_excludes_closed_past_and_far_future(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-19 08:00:00', 'Asia/Ho_Chi_Minh'));
        $this->makeInquiry('Khach trong tuan', 'contacted', '2026-11-20 02:00:00');
        $this->makeInquiry('Khach da tre', 'contacted', '2026-11-18 02:00:00');
        $this->makeInquiry('Khach sau hai tuan', 'contacted', '2026-12-05 02:00:00');
        $this->makeInquiry('Khach da dong', 'closed', '2026-11-20 02:00:00');
        $this->actingAs($this->admin(), 'admin')
            ->get(route('admin.saas-inquiries.index', ['due' => 'next7']))
            ->assertOk()->assertSee('Khach trong tuan')
            ->assertDontSee('Khach da tre')
            ->assertDontSee('Khach sau hai tuan')
            ->assertDontSee('Khach da dong');
    }

    public function test_show_update_back_link_preserves_only_safe_list_filters(): void
    {
        $id = $this->makeInquiry('Khach quay lai', 'new');
        $filters = [
            'status' => 'new', 'assigned' => 'unassigned', 'due' => 'overdue',
            'sort' => 'next', 'q' => 'quay lai', 'page' => 2,
        ];
        $admin = $this->admin();
        $this->actingAs($admin, 'admin');
        $show = route('admin.saas-inquiries.show', ['id' => $id] + $filters);
        $back = route('admin.saas-inquiries.index', $filters);
        $this->get($show)->assertOk()->assertSee('Quay lại danh sách')
            ->assertSee(e($back), false)->assertSee(e(route('admin.saas-inquiries.update', ['id' => $id] + $filters)), false);
        $this->patch(route('admin.saas-inquiries.update', ['id' => $id] + $filters), [
            'status' => 'contacted', 'admin_note' => 'Đã gọi',
        ])->assertRedirect($show)->assertSessionHas('success');
        $this->assertDatabaseHas('saas_inquiries', ['id' => $id, 'status' => 'contacted']);
        $this->assertDatabaseHas('saas_inquiry_activities', ['saas_inquiry_id' => $id, 'note' => 'Đã gọi']);

        $unsafe = route('admin.saas-inquiries.show', [
            'id' => $id, 'sort' => 'malicious', 'due' => 'bogus', 'page' => '999999999',
            'redirect' => 'https://example.org',
        ]);
        // Kiểm tra chinh xac URL dieu huong; email Admin co the dung @example.org.
        // Khong kiem tra 'example.org' tren toan HTML vi email do la du lieu hop le.
        $this->get($unsafe)->assertOk()
            ->assertSee('href="'.e(route('admin.saas-inquiries.index')).'"', false)
            ->assertSee('action="'.e(route('admin.saas-inquiries.update', $id)).'"', false)
            ->assertDontSee('redirect=', false)
            ->assertDontSee('sort=malicious', false)
            ->assertDontSee('due=bogus', false)
            ->assertDontSee('page=999999999', false);
    }

    public function test_old_inquiry_workflow_still_updates_without_filter_parameters(): void
    {
        $id = $this->makeInquiry('Khach tu van', 'new');
        $this->actingAs($this->admin(), 'admin')->patch(route('admin.saas-inquiries.update', $id), [
            'status' => 'contacted', 'admin_note' => 'Hẹn gọi lại',
        ])->assertRedirect(route('admin.saas-inquiries.show', $id));
        $this->assertDatabaseHas('saas_inquiry_activities', ['saas_inquiry_id' => $id, 'note' => 'Hẹn gọi lại']);
    }
}
