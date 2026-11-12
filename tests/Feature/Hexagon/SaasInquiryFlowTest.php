<?php

declare(strict_types=1);

namespace Tests\Feature\Hexagon;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\Support\HexagonIsolatedTestCase;

final class SaasInquiryFlowTest extends HexagonIsolatedTestCase
{
    private function service(bool $active = true): void
    {
        DB::table('saas_services')->insert([
            'slug' => 'quan-ly-ban-hang',
            'name_vi' => 'Quản lý bán hàng',
            'is_active' => $active,
            'is_featured' => false,
            'sort_order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_guest_can_send_consultation_for_active_service(): void
    {
        $this->service();
        $this->post(route('saas.inquiries.store', 'quan-ly-ban-hang'), [
            'customer_name' => 'Nguyễn Văn A',
            'customer_email' => 'CUSTOMER@EXAMPLE.COM',
            'customer_phone' => '0912345678',
            'message' => 'Cần dùng cho 10 nhân viên',
            'consent' => '1',
        ])->assertRedirect(route('saas.show', 'quan-ly-ban-hang'))
            ->assertSessionHas('saas_inquiry_success');

        $this->assertDatabaseHas('saas_inquiries', [
            'customer_name' => 'Nguyễn Văn A',
            'customer_email' => 'customer@example.com',
            'status' => 'new',
            'service_name' => 'Quản lý bán hàng',
        ]);
    }

    public function test_hidden_service_cannot_receive_enquiries(): void
    {
        $this->service(false);
        $this->post(route('saas.inquiries.store', 'quan-ly-ban-hang'), [
            'customer_name' => 'Nguyễn Văn A',
            'customer_email' => 'customer@example.com',
            'customer_phone' => '0912345678',
            'consent' => '1',
        ])->assertNotFound();
        $this->assertDatabaseCount('saas_inquiries', 0);
    }

    public function test_consent_and_phone_are_validated(): void
    {
        $this->service();
        $this->post(route('saas.inquiries.store', 'quan-ly-ban-hang'), [
            'customer_name' => 'Nguyễn Văn A',
            'customer_email' => 'customer@example.com',
            'customer_phone' => '123',
        ])->assertSessionHasErrors(['customer_phone', 'consent']);
        $this->assertDatabaseCount('saas_inquiries', 0);
    }

    public function test_guest_and_customer_cannot_read_admin_inquiries(): void
    {
        $this->get(route('admin.saas-inquiries.index'))->assertRedirect(route('login'));
        $customer = User::factory()->create(['is_admin' => false]);
        $this->actingAs($customer, 'web')->get(route('admin.saas-inquiries.index'))->assertForbidden();
    }

    public function test_admin_can_review_and_update_inquiry(): void
    {
        $this->service();
        $this->post(route('saas.inquiries.store', 'quan-ly-ban-hang'), [
            'customer_name' => 'Nguyễn Văn A',
            'customer_email' => 'customer@example.com',
            'customer_phone' => '0912345678',
            'consent' => '1',
        ])->assertRedirect();

        $id = (int) DB::table('saas_inquiries')->value('id');
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin, 'admin');

        $this->get(route('admin.saas-inquiries.index'))->assertOk()->assertSee('Nguyễn Văn A');
        $this->get(route('admin.saas-inquiries.show', $id))->assertOk()->assertSee('0912345678');
        $this->patch(route('admin.saas-inquiries.update', $id), [
            'status' => 'contacted',
            'admin_note' => 'Đã gọi điện',
        ])->assertRedirect(route('admin.saas-inquiries.show', $id));

        $this->assertDatabaseHas('saas_inquiries', ['id' => $id, 'status' => 'contacted', 'admin_note' => 'Đã gọi điện']);
    }

    public function test_invalid_admin_status_is_rejected(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin, 'admin')
            ->patch(route('admin.saas-inquiries.update', 10), [
                'status' => 'approved',
            ])->assertSessionHasErrors('status');
    }
}
