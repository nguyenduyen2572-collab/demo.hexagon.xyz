<?php

namespace Tests\Feature\Hexagon;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\Support\HexagonIsolatedTestCase;

class AdminSaasListV82Test extends HexagonIsolatedTestCase
{
    private function addService(string $slug, string $name, bool $active, int $order = 0): int
    {
        return (int) DB::table('saas_services')->insertGetId([
            'slug' => $slug,
            'name_vi' => $name,
            'price_monthly' => 199000,
            'is_active' => $active,
            'is_featured' => false,
            'sort_order' => $order,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_admin_filters_by_name_slug_and_visibility(): void
    {
        $this->addService('phan-mem-ban-hang', 'Phần mềm bán hàng', true);
        $this->addService('crm-doanh-nghiep', 'Quản lý khách hàng CRM', false);
        $this->addService('quan-ly-kho', 'Quản lý kho hàng', true);
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin, 'admin')
            ->get(route('admin.saas.index', ['q' => 'crm', 'status' => 'hidden']))
            ->assertOk()->assertSee('Quản lý khách hàng CRM')
            ->assertDontSee('Phần mềm bán hàng')
            ->assertDontSee('Quản lý kho hàng')
            ->assertSee('Tìm thấy');

        $this->actingAs($admin, 'admin')
            ->get(route('admin.saas.index', ['q' => 'ban-hang']))
            ->assertOk()->assertSee('Phần mềm bán hàng')
            ->assertDontSee('Quản lý khách hàng CRM');
    }

    public function test_empty_filter_result_and_escaped_search_input(): void
    {
        $this->addService('demo', 'Dịch vụ quản lý', true);
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin, 'admin')
            ->get(route('admin.saas.index', ['q' => '<script>alert(1)</script>']))
            ->assertOk()
            ->assertSee('Không tìm thấy dịch vụ phù hợp.')
            ->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_filter_and_page_are_preserved_when_hiding_service(): void
    {
        $id = $this->addService('dich-vu-a', 'Dịch vụ A', true);
        $admin = User::factory()->create(['is_admin' => true]);
        $url = route('admin.saas.toggle', [
            'id' => $id, 'q' => 'dich-vu', 'status' => 'active', 'page' => '2',
        ]);
        $this->actingAs($admin, 'admin')->patch($url)
            ->assertRedirect(route('admin.saas.index', [
                'q' => 'dich-vu', 'status' => 'active', 'page' => 2,
            ]));
        $this->assertDatabaseHas('saas_services', ['id' => $id, 'is_active' => 0]);
    }

    public function test_edit_screen_keeps_return_to_list_filters(): void
    {
        $id = $this->addService('dich-vu-a', 'Dịch vụ A', true);
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin, 'admin')
            ->get(route('admin.saas.edit', ['id' => $id, 'q' => 'dịch vụ', 'status' => 'active', 'page' => 3]))
            ->assertOk()
            ->assertSee('page=3', false)
            ->assertSee('status=active', false);
    }
}
