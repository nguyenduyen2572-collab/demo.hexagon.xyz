<?php

namespace Tests\Feature\Hexagon;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\Support\HexagonIsolatedTestCase;

class SaasAdminEditorV2Test extends HexagonIsolatedTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config()->set('services.hexagon_saas_translation.provider', 'mymemory');
        // Prevent accidental real external requests while running tests.
        Http::preventStrayRequests();
    }

    private function fakeTranslation(): void
    {
        Http::fake(['api.mymemory.translated.net/*' => Http::response([
            'responseStatus' => 200, 'responseData' => ['translatedText' => 'Sales management software'],
        ], 200)]);
    }

    public function test_vietnamese_name_auto_generates_slug_and_formatted_vnd_price_is_saved_as_integer(): void
    {
        $this->fakeTranslation();
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin, 'admin')->post(route('admin.saas.store'), [
            'name_vi' => 'Phần mềm quản lý bán hàng',
            'slug' => '', // backend also supports JavaScript disabled
            'summary_vi' => 'Quản lý đơn hàng và doanh thu.',
            'price_monthly' => '199.000',
            'sort_order' => 1,
            'is_active' => 1,
        ])->assertRedirect(route('admin.saas.index'));

        $this->assertDatabaseHas('saas_services', [
            'slug' => 'phan-mem-quan-ly-ban-hang',
            'price_monthly' => 199000,
            'name_en' => 'Sales management software',
            'is_active' => 1,
        ]);
        $this->get(route('saas.index'))->assertOk()->assertSee('199.000đ');
    }

    public function test_monthly_price_rejects_fractional_values_without_changing_them(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin, 'admin')->post(route('admin.saas.store'), [
            'name_vi' => 'Phần mềm bán hàng',
            'slug' => 'phan-mem-ban-hang',
            'price_monthly' => '199.5',
            'sort_order' => 0,
        ])->assertSessionHasErrors(['price_monthly']);
        $this->assertDatabaseCount('saas_services', 0);
    }

    public function test_malformed_currency_and_negative_values_are_rejected(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        foreach (['199,000', '-100', '1.23', '5e6'] as $value) {
            $this->actingAs($admin, 'admin')->post(route('admin.saas.store'), [
                'name_vi' => 'Dịch vụ kiểm thử', 'slug' => 'dich-vu-kiem-thu',
                'price_monthly' => $value, 'sort_order' => 0,
            ])->assertSessionHasErrors(['price_monthly']);
        }
        $this->assertDatabaseCount('saas_services', 0);
    }

    public function test_changing_only_price_preserves_existing_translation_without_api_call(): void
    {
        DB::table('saas_services')->insert([
            'slug' => 'crm', 'name_vi' => 'Quản lý khách hàng', 'name_en' => 'Customer Management',
            'summary_vi' => 'Giúp quản lý khách hàng', 'summary_en' => 'Manage customers',
            'description_vi' => null, 'description_en' => null,
            'price_monthly' => 199000, 'sort_order' => 0, 'is_active' => 1, 'is_featured' => 0,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $id = DB::table('saas_services')->where('slug', 'crm')->value('id');
        $admin = User::factory()->create(['is_admin' => true]);
        Http::fake();
        $this->actingAs($admin, 'admin')->patch(route('admin.saas.update', $id), [
            'slug' => 'crm', 'name_vi' => 'Quản lý khách hàng',
            'summary_vi' => 'Giúp quản lý khách hàng',
            'price_monthly' => '299.000', 'sort_order' => 0, 'is_active' => 1,
        ])->assertRedirect(route('admin.saas.index'));
        Http::assertNothingSent();
        $this->assertDatabaseHas('saas_services', [
            'slug' => 'crm', 'name_en' => 'Customer Management', 'summary_en' => 'Manage customers',
            'price_monthly' => 299000,
        ]);
    }

    public function test_translation_failure_prevents_incomplete_english_data_being_saved(): void
    {
        Http::fake(['api.mymemory.translated.net/*' => Http::response([
            'responseStatus' => 429, 'responseData' => ['translatedText' => ''],
        ], 429)]);
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin, 'admin')->post(route('admin.saas.store'), [
            'name_vi' => 'Quản lý dự án', 'slug' => 'quan-ly-du-an',
            'price_monthly' => '149.000', 'sort_order' => 0,
        ])->assertSessionHasErrors(['name_vi']);
        $this->assertDatabaseCount('saas_services', 0);
    }

    public function test_translation_preview_is_accessible_only_to_admin_and_returns_text(): void
    {
        $this->postJson(route('admin.saas.translate-preview'), [
            'field' => 'name_vi', 'text' => 'Quản lý bán hàng',
        ])->assertUnauthorized();
        $customer = User::factory()->create(['is_admin' => false]);
        $this->actingAs($customer, 'web')->postJson(route('admin.saas.translate-preview'), [
            'field' => 'name_vi', 'text' => 'Quản lý bán hàng',
        ])->assertForbidden();
        $admin = User::factory()->create(['is_admin' => true]);
        $this->fakeTranslation();
        $this->actingAs($admin, 'admin')->postJson(route('admin.saas.translate-preview'), [
            'field' => 'name_vi', 'text' => 'Quản lý bán hàng',
        ])->assertOk()->assertJsonPath('translation', 'Sales management software');
    }
}
