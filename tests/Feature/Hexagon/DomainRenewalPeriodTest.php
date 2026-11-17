<?php

namespace Tests\Feature\Hexagon;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\Support\HexagonIsolatedTestCase;

class DomainRenewalPeriodTest extends HexagonIsolatedTestCase
{
    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    private function newPrice(array $changes = []): array
    {
        return array_merge([
            'extension' => '.renewaltest',
            'registration_price' => 260000,
            'renewal_price' => 350000,
            'renewal_period_months' => 12,
        ], $changes);
    }

    public function test_migration_adds_period_and_existing_rows_default_to_twelve_months(): void
    {
        $this->assertTrue(Schema::hasColumn('domain_prices', 'renewal_period_months'));
        DB::table('domain_prices')->insert([
            'extension' => '.oldrenewal',
            'registration_price' => 260000,
            'renewal_price' => 350000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->assertDatabaseHas('domain_prices', [
            'extension' => '.oldrenewal',
            'renewal_period_months' => 12,
        ]);
    }

    public function test_admin_can_add_and_view_renewal_period(): void
    {
        $this->actingAs($this->admin(), 'admin')
            ->post(route('admin.domain-prices.store'), $this->newPrice([
                'renewal_period_months' => 6,
            ]))
            ->assertRedirect(route('admin.domain-prices.index'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('domain_prices', [
            'extension' => '.renewaltest',
            'renewal_price' => 350000,
            'renewal_period_months' => 6,
        ]);

        $this->get(route('admin.domain-prices.index'))
            ->assertOk()
            ->assertSee('Thời hạn gia hạn')
            ->assertSee('6 tháng')
            ->assertSee('350.000đ');
    }

    public function test_admin_can_update_period_without_changing_existing_price_logic(): void
    {
        $id = DB::table('domain_prices')->insertGetId([
            'extension' => '.updateperiod',
            'registration_price' => 260000,
            'renewal_price' => 350000,
            'renewal_period_months' => 12,
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($this->admin(), 'admin')
            ->get(route('admin.domain-prices.edit', ['id' => $id]))
            ->assertOk()
            ->assertSee('name="renewal_period_months"', false);

        $this->patch(route('admin.domain-prices.update', ['id' => $id]), [
            'registration_price' => 270000,
            'renewal_price' => 360000,
            'renewal_period_months' => 24,
        ])->assertRedirect(route('admin.domain-prices.index'))
          ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('domain_prices', [
            'id' => $id,
            'registration_price' => 270000,
            'renewal_price' => 360000,
            'renewal_period_months' => 24,
            'is_active' => 1,
        ]);
    }

    public function test_invalid_or_missing_period_is_rejected(): void
    {
        $this->actingAs($this->admin(), 'admin');
        foreach ([0, 2, 36, '3.5', 'abc'] as $invalid) {
            $this->post(route('admin.domain-prices.store'), $this->newPrice([
                'renewal_period_months' => $invalid,
            ]))->assertSessionHasErrors('renewal_period_months');
        }
        $payload = $this->newPrice();
        unset($payload['renewal_period_months']);
        $this->post(route('admin.domain-prices.store'), $payload)
            ->assertSessionHasErrors('renewal_period_months');
        $this->assertDatabaseMissing('domain_prices', ['extension' => '.renewaltest']);
    }

    public function test_customer_cannot_manage_domain_renewal_prices(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => false]))
            ->post(route('admin.domain-prices.store'), $this->newPrice())
            ->assertForbidden();
    }
}
