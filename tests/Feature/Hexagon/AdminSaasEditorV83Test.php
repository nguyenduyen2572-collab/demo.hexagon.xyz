<?php

namespace Tests\Feature\Hexagon;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\Support\HexagonIsolatedTestCase;

class AdminSaasEditorV83Test extends HexagonIsolatedTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config()->set('services.hexagon_saas_translation.provider', 'mymemory');
        Http::preventStrayRequests();
    }

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    private function failTranslation(): void
    {
        Http::fake(['api.mymemory.translated.net/*' => Http::response([
            'responseStatus' => 429, 'responseData' => ['translatedText' => ''],
        ], 429)]);
    }

    private function workingTranslation(): void
    {
        Http::fake(['api.mymemory.translated.net/*' => Http::response([
            'responseStatus' => 200, 'responseData' => ['translatedText' => 'Project management'],
        ], 200)]);
    }

    private function createDraftViaFailure(bool $fakeApiFailure = true): int
    {
        if ($fakeApiFailure) {
            $this->failTranslation();
        }
        $this->actingAs($this->admin(), 'admin')->post(route('admin.saas.store'), [
            'name_vi' => 'Quản lý dự án', 'slug' => 'quan-ly-du-an',
            'price_monthly' => '149.000', 'sort_order' => 0,
            'is_active' => 1, 'is_featured' => 1,
            'save_as_draft_on_translation_failure' => 1,
        ])->assertRedirect(route('admin.saas.index'))->assertSessionHas('warning');
        return (int) DB::table('saas_services')->where('slug', 'quan-ly-du-an')->value('id');
    }

    public function test_translation_failure_can_explicitly_save_hidden_draft(): void
    {
        $id = $this->createDraftViaFailure();
        $this->assertGreaterThan(0, $id);
        $this->assertDatabaseHas('saas_services', [
            'id' => $id, 'name_en' => null, 'is_active' => 0, 'is_featured' => 1,
        ]);
        $this->get(route('saas.index'))->assertOk()->assertDontSee('Quản lý dự án');
    }

    public function test_manual_english_is_saved_without_translation_api(): void
    {
        Http::fake();
        $this->actingAs($this->admin(), 'admin')->post(route('admin.saas.store'), [
            'name_vi' => 'Quản lý dự án', 'slug' => 'quan-ly-du-an',
            'summary_vi' => 'Tiến độ công việc', 'description_vi' => 'Thống kê dự án',
            'name_en' => 'Project Management', 'summary_en' => 'Task progress',
            'description_en' => 'Project reports',
            'translation_mode' => 'manual', 'sort_order' => 0, 'is_active' => 1,
        ])->assertRedirect(route('admin.saas.index'))->assertSessionHas('success');
        Http::assertNothingSent();
        $this->assertDatabaseHas('saas_services', [
            'slug' => 'quan-ly-du-an', 'name_en' => 'Project Management',
            'summary_en' => 'Task progress', 'description_en' => 'Project reports',
            'is_active' => 1,
        ]);
    }

    public function test_manual_mode_requires_complete_english_for_entered_vietnamese(): void
    {
        Http::fake();
        $this->actingAs($this->admin(), 'admin')->post(route('admin.saas.store'), [
            'name_vi' => 'Quản lý dự án', 'slug' => 'quan-ly-du-an',
            'summary_vi' => 'Tiến độ công việc',
            'translation_mode' => 'manual', 'name_en' => '', 'summary_en' => '',
            'sort_order' => 0,
        ])->assertSessionHasErrors(['name_en', 'summary_en']);
        Http::assertNothingSent();
        $this->assertDatabaseCount('saas_services', 0);
    }

    public function test_automatic_mode_ignores_untrusted_english_fields(): void
    {
        $this->workingTranslation();
        $this->actingAs($this->admin(), 'admin')->post(route('admin.saas.store'), [
            'name_vi' => 'Quản lý dự án', 'slug' => 'quan-ly-du-an',
            'name_en' => 'INJECTED', 'translation_mode' => 'auto', 'sort_order' => 0,
        ])->assertRedirect(route('admin.saas.index'));
        $this->assertDatabaseHas('saas_services', ['slug' => 'quan-ly-du-an', 'name_en' => 'Project management']);
    }

    public function test_draft_cannot_be_published_without_english_translation(): void
    {
        $id = $this->createDraftViaFailure();
        $this->actingAs($this->admin(), 'admin')->patch(route('admin.saas.toggle', $id))
            ->assertRedirect(route('admin.saas.index'))->assertSessionHas('warning');
        $this->assertDatabaseHas('saas_services', ['id' => $id, 'is_active' => 0]);
    }

    public function test_active_service_is_hidden_if_edited_content_cannot_be_translated_and_draft_is_chosen(): void
    {
        $now = now();
        $id = DB::table('saas_services')->insertGetId([
            'slug' => 'crm', 'name_vi' => 'Quản lý khách hàng', 'name_en' => 'Customer Management',
            'summary_vi' => null, 'summary_en' => null,
            'description_vi' => null, 'description_en' => null,
            'price_monthly' => 199000, 'sort_order' => 0,
            'is_active' => 1, 'is_featured' => 1, 'created_at' => $now, 'updated_at' => $now,
        ]);
        $this->failTranslation();
        $this->actingAs($this->admin(), 'admin')->patch(route('admin.saas.update', $id), [
            'name_vi' => 'Quản lý khách hàng nâng cao', 'slug' => 'crm', 'sort_order' => 0,
            'is_active' => 1, 'is_featured' => 1,
            'save_as_draft_on_translation_failure' => 1,
        ])->assertRedirect(route('admin.saas.index'))->assertSessionHas('warning');
        $this->assertDatabaseHas('saas_services', [
            'id' => $id, 'name_vi' => 'Quản lý khách hàng nâng cao',
            'name_en' => null, 'is_active' => 0, 'is_featured' => 1,
        ]);
    }

    public function test_retrying_auto_translation_allows_publishing_draft(): void
    {
        // HTTP fakes are additive: one response sequence models failure then recovery.
        Http::fake([
            'api.mymemory.translated.net/*' => Http::sequence()
                ->push(['responseStatus' => 429, 'responseData' => ['translatedText' => '']], 429)
                ->push(['responseStatus' => 200, 'responseData' => ['translatedText' => 'Project management']], 200),
        ]);
        $id = $this->createDraftViaFailure(false);
        $this->actingAs($this->admin(), 'admin')->patch(route('admin.saas.update', $id), [
            'name_vi' => 'Quản lý dự án', 'slug' => 'quan-ly-du-an',
            'sort_order' => 0, 'is_active' => 1,
        ])->assertRedirect(route('admin.saas.index'))->assertSessionHas('success');
        $this->assertDatabaseHas('saas_services', [
            'id' => $id, 'name_en' => 'Project management', 'is_active' => 1,
        ]);
    }
}
