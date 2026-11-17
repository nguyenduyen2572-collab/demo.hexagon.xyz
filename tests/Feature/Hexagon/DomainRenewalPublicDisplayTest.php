<?php
namespace Tests\Feature\Hexagon;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\Support\HexagonIsolatedTestCase;

class DomainRenewalPublicDisplayTest extends HexagonIsolatedTestCase
{
    public function test_homepage_shows_saved_months_and_current_price_after_admin_changes(): void
    {
        $this->assertTrue(Schema::hasColumn('domain_prices', 'renewal_period_months'));
        $id = DB::table('domain_prices')->insertGetId([
            'extension' => '.publicrenewal',
            'registration_price' => 260000,
            'renewal_price' => 350000,
            'renewal_period_months' => 12,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->get('/')
            ->assertOk()
            ->assertSee('data-renewal-months="12"', false)
            ->assertSee('/12 tháng', false)
            ->assertDontSee('Tìm tên miền ngay');

        $this->actingAs(User::factory()->create(['is_admin' => true]), 'admin')
            ->patch(route('admin.domain-prices.update', ['id' => $id]), [
                'registration_price' => 260000,
                'renewal_price' => 395000,
                'renewal_period_months' => 3,
            ])->assertRedirect(route('admin.domain-prices.index'))
              ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('domain_prices', [
            'id' => $id, 'renewal_price' => 395000, 'renewal_period_months' => 3,
        ]);
        $this->get('/')
            ->assertOk()
            ->assertSee('data-renewal-months="3"', false)
            ->assertSee('/3 tháng', false)
            ->assertSee('395.000đ', false)
            ->assertDontSee('Tìm tên miền ngay');
    }

    public function test_domain_check_api_returns_saved_renewal_months(): void
    {
        DB::table('domain_prices')->insert([
            'extension' => '.renewalapi',
            'registration_price' => 260000,
            'renewal_price' => 390000,
            'renewal_period_months' => 6,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('mock_domain_statuses')->insert([
            'domain' => 'hellopublicrenewal.renewalapi',
            'is_available' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->getJson('/api/v1/domains/check?domain=hellopublicrenewal.renewalapi')
            ->assertOk()
            ->assertJsonPath('data.renewal_price', 390000)
            ->assertJsonPath('data.renewal_period_months', 6);
    }
}
