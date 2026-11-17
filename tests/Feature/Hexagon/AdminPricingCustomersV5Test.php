<?php

declare(strict_types=1);

namespace Tests\Feature\Hexagon;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\Support\HexagonIsolatedTestCase;

final class AdminPricingCustomersV5Test extends HexagonIsolatedTestCase
{
    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    private function price(string $extension, bool $active = true): int
    {
        return DB::table('domain_prices')->insertGetId([
            'extension' => $extension,
            'registration_price' => 250000,
            'renewal_price' => 320000,
            'renewal_period_months' => 12,
            'is_active' => $active,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function order(User $customer, string $status): void
    {
        DB::table('orders')->insert([
            'order_code' => (string) Str::uuid(),
            'customer_name' => $customer->name,
            'customer_email' => $customer->email,
            'customer_phone' => '0900000000',
            'total_amount' => 250000,
            'status' => $status,
            'is_mock' => true,
            'user_id' => $customer->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_price_search_status_sort_and_pagination(): void
    {
        for ($i = 1; $i <= 14; $i++) {
            $this->price(sprintf('.v5%02d', $i));
        }
        $this->price('.v5off', false);
        $this->price('.different');

        $this->actingAs($this->admin(), 'admin')
            ->get(route('admin.domain-prices.index', ['q' => '.v5', 'status' => 'active', 'sort' => 'extension']))
            ->assertOk()
            ->assertViewHas('prices', fn ($prices) => $prices->total() === 14 && $prices->count() === 12)
            ->assertSee('.v501')->assertDontSee('.v5off');

        $this->get(route('admin.domain-prices.index', ['q' => '.v5', 'status' => 'active', 'sort' => 'extension', 'page' => 2]))
            ->assertOk()
            ->assertViewHas('prices', fn ($prices) => $prices->total() === 14 && $prices->count() === 2)
            ->assertSee('name="q"', false);

        $this->get(route('admin.domain-prices.index', ['q' => '.v5', 'status' => 'inactive']))
            ->assertOk()->assertSee('.v5off')->assertDontSee('.v501');

        $this->get(route('admin.domain-prices.index', ['sort' => 'arbitrary']))
            ->assertSessionHasErrors('sort');
    }

    public function test_audit_records_create_update_toggle_but_not_noop(): void
    {
        $this->assertTrue(Schema::hasTable('domain_price_histories'));
        $admin = $this->admin();
        $this->actingAs($admin, 'admin')
            ->post(route('admin.domain-prices.store'), [
                'extension' => '.v5audit',
                'registration_price' => 270000,
                'renewal_price' => 340000,
                'renewal_period_months' => 6,
            ])->assertRedirect(route('admin.domain-prices.index'));

        $id = (int) DB::table('domain_prices')->where('extension', '.v5audit')->value('id');
        $this->assertDatabaseHas('domain_price_histories', [
            'domain_price_id' => $id,
            'action' => 'created',
            'admin_user_id' => $admin->id,
            'old_registration_price' => null,
            'new_registration_price' => 270000,
        ]);

        $this->patch(route('admin.domain-prices.update', ['id' => $id]), [
            'registration_price' => 290000,
            'renewal_price' => 340000,
            'renewal_period_months' => 12,
        ])->assertRedirect(route('admin.domain-prices.index'));

        $this->assertDatabaseHas('domain_price_histories', [
            'domain_price_id' => $id,
            'action' => 'updated',
            'old_registration_price' => 270000,
            'new_registration_price' => 290000,
            'old_renewal_period_months' => 6,
            'new_renewal_period_months' => 12,
        ]);
        $this->patch(route('admin.domain-prices.update', ['id' => $id]), [
            'registration_price' => 290000,
            'renewal_price' => 340000,
            'renewal_period_months' => 12,
        ])->assertRedirect(route('admin.domain-prices.index'));

        $this->patch(route('admin.domain-prices.toggle', ['id' => $id]))
            ->assertRedirect();
        $this->assertDatabaseHas('domain_price_histories', [
            'domain_price_id' => $id,
            'action' => 'toggled',
            'old_is_active' => 1,
            'new_is_active' => 0,
        ]);
        $this->assertSame(3, DB::table('domain_price_histories')->where('domain_price_id', $id)->count());
        $this->get(route('admin.domain-prices.edit', ['id' => $id]))
            ->assertOk()->assertSee('Lịch sử điều chỉnh')->assertSee('Điều chỉnh bảng giá');
    }

    public function test_customer_filters_and_status_are_limited_to_linked_user_orders(): void
    {
        $needs = User::factory()->create(['name' => 'Khach Need V5', 'is_admin' => false]);
        $noOrders = User::factory()->create(['name' => 'Khach Empty V5', 'is_admin' => false]);
        $completed = User::factory()->create(['name' => 'Khach Done V5', 'is_admin' => false]);
        $this->order($needs, 'pending_demo');
        $this->order($needs, 'completed_demo');
        $this->order($completed, 'completed_demo');

        $this->actingAs($this->admin(), 'admin')
            ->get(route('admin.customers.index', ['activity' => 'needs_action']))
            ->assertOk()->assertSee('Khach Need V5')
            ->assertDontSee('Khach Empty V5')->assertDontSee('Khach Done V5');

        $this->get(route('admin.customers.index', ['activity' => 'no_orders']))
            ->assertOk()->assertSee('Khach Empty V5')
            ->assertDontSee('Khach Need V5')->assertDontSee('Khach Done V5');

        $this->get(route('admin.customers.show', ['id' => $needs->id, 'status' => 'needs_action']))
            ->assertOk()
            ->assertViewHas('orders', fn ($orders) => $orders->total() === 1 && $orders->first()->status === 'pending_demo');

        $this->get(route('admin.customers.show', ['id' => $needs->id, 'status' => 'completed_demo']))
            ->assertOk()
            ->assertViewHas('orders', fn ($orders) => $orders->total() === 1 && $orders->first()->status === 'completed_demo');

        $this->get(route('admin.customers.show', ['id' => $needs->id, 'status' => 'invalid']))
            ->assertSessionHasErrors('status');
    }

    public function test_regular_user_cannot_access_price_management_or_customer_insights(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user)
            ->get(route('admin.domain-prices.index'))->assertForbidden();
        $this->get(route('admin.customers.index'))->assertForbidden();
        $this->patch(route('admin.domain-prices.toggle', ['id' => 1]))->assertForbidden();
    }
}
