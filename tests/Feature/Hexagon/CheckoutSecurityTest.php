<?php

namespace Tests\Feature\Hexagon;

use App\Models\User;

use Illuminate\Support\Facades\DB;
use Tests\Support\HexagonIsolatedTestCase;

class CheckoutSecurityTest extends HexagonIsolatedTestCase
{
    private function insertMockDomain(bool $active, int $price = 260000): void
    {
        DB::table('domain_prices')->insert([
            'extension' => '.com',
            'registration_price' => $price,
            'renewal_price' => 350000,
            'is_active' => $active,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('mock_domain_statuses')->insert([
            'domain' => 'hexagonshop.com',
            'is_available' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function demoCart(int $fakeBrowserPrice = 1): array
    {
        return [
            'hexagonshop.com' => [
                'domain' => 'hexagonshop.com',
                'extension' => '.com',
                'registration_price' => $fakeBrowserPrice,
            ],
        ];
    }

    private function checkoutInfo(): array
    {
        return [
            'customer_name' => 'Khach kiem thu',
            'customer_email' => 'testing@example.test',
            'customer_phone' => '0900000000',
        ];
    }

    public function test_inactive_extension_cannot_be_ordered(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => false]));

        $this->insertMockDomain(active: false);

        $this->withSession(['hexagon_cart' => $this->demoCart()])
            ->post(route('checkout.store'), $this->checkoutInfo())
            ->assertRedirect(route('cart.index'));

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_checkout_uses_database_price_not_session_price(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => false]));

        $this->insertMockDomain(active: true, price: 260000);

        $this->withSession(['hexagon_cart' => $this->demoCart(1)])
            ->post(route('checkout.store'), $this->checkoutInfo())
            ->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'total_amount' => 260000,
            'customer_email' => 'testing@example.test',
        ]);

        $this->assertDatabaseHas('order_items', [
            'domain_name' => 'hexagonshop.com',
            'registration_price' => 260000,
            'line_total' => 260000,
        ]);
    }
}
