<?php

namespace Tests\Feature\Hexagon;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\Support\HexagonIsolatedTestCase;

/**
 * Regression checks for checkout retries and order-confirmation access.
 * These tests are SEQUENTIAL: they do not simulate two simultaneous HTTP requests.
 */
class CheckoutDuplicateSubmissionTest extends HexagonIsolatedTestCase
{
    private function seedSellableDomain(): void
    {
        DB::table('domain_prices')->insert([
            'extension' => '.com',
            'registration_price' => 260000,
            'renewal_price' => 350000,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('mock_domain_statuses')->insert([
            'domain' => 'hexagon-retry.com',
            'is_available' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function sampleCart(): array
    {
        return [
            'hexagon-retry.com' => [
                'domain' => 'hexagon-retry.com',
                'extension' => '.com',
                'registration_price' => 1, // Never trust a browser/session price.
            ],
        ];
    }

    private function buyer(): array
    {
        return [
            'customer_name' => 'Khach kiem thu',
            'customer_email' => 'checkout-retry@example.test',
            'customer_phone' => '0900000000',
        ];
    }

    public function test_repeating_checkout_after_success_does_not_create_a_second_order(): void
    {
        $this->seedSellableDomain();

        $this->withSession(['hexagon_cart' => $this->sampleCart()])
            ->post(route('checkout.store'), $this->buyer())
            ->assertRedirect();

        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('order_items', 1);

        // A second POST from the same browser session, after the first finishes.
        $this->post(route('checkout.store'), $this->buyer())
            ->assertRedirect(route('cart.index'));

        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('order_items', 1);
    }

    public function test_empty_cart_checkout_does_not_create_an_order(): void
    {
        $this->withSession(['hexagon_cart' => []])
            ->post(route('checkout.store'), $this->buyer())
            ->assertRedirect(route('cart.index'));

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
    }

    public function test_order_receipt_is_forbidden_without_session_authorization(): void
    {
        $code = (string) Str::uuid();

        DB::table('orders')->insert([
            'order_code' => $code,
            'user_id' => null,
            'customer_name' => 'Khach kiem thu',
            'customer_email' => 'checkout-retry@example.test',
            'customer_phone' => '0900000000',
            'total_amount' => 260000,
            'status' => 'pending_demo',
            'is_mock' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->withSession(['hexagon_order_codes' => []])
            ->get(route('orders.show', ['code' => $code]))
            ->assertForbidden();
    }
}
