<?php

namespace Tests\Feature\Hexagon;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\Support\HexagonIsolatedTestCase;

class SaasCatalogTest extends HexagonIsolatedTestCase
{
    private function createService(string $slug, bool $active): void
    {
        DB::table('saas_services')->insert([
            'slug' => $slug,
            'name_vi' => 'SaaS mẫu ' . $slug,
            'price_monthly' => 199000,
            'is_active' => $active,
            'is_featured' => false,
            'sort_order' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_public_only_sees_active_saas_services(): void
    {
        $this->createService('active-saas', true);
        $this->createService('hidden-saas', false);

        $this->get(route('saas.index'))
            ->assertOk()
            ->assertSee('SaaS mẫu active-saas')
            ->assertDontSee('SaaS mẫu hidden-saas');
    }

    public function test_hidden_service_detail_is_not_accessible(): void
    {
        $this->createService('hidden-saas', false);
        $this->get(route('saas.show', 'hidden-saas'))->assertNotFound();
    }

    public function test_regular_customer_cannot_manage_saas_services(): void
    {
        $customer = User::factory()->create(['is_admin' => false]);
        $this->actingAs($customer)
            ->get(route('admin.saas.index'))
            ->assertForbidden();
    }

    public function test_admin_can_create_a_new_service(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin, 'admin')->post(route('admin.saas.store'), [
            'slug' => 'demo-saas',
            'name_vi' => 'Phần mềm Demo',
            'summary_vi' => 'Mô tả mẫu',
            'price_monthly' => 250000,
            'sort_order' => 0,
            'is_active' => 1,
        ])->assertRedirect(route('admin.saas.index'));

        $this->assertDatabaseHas('saas_services', [
            'slug' => 'demo-saas',
            'price_monthly' => 250000,
            'is_active' => 1,
        ]);
    }

    public function test_admin_can_hide_an_active_service(): void
    {
        $this->createService('demo-saas', true);
        $id = DB::table('saas_services')->where('slug', 'demo-saas')->value('id');
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin, 'admin')
            ->patch(route('admin.saas.toggle', $id))
            ->assertRedirect(route('admin.saas.index'));

        $this->get(route('saas.show', 'demo-saas'))->assertNotFound();
    }
}
