<?php

namespace Tests\Feature\Hexagon;

use App\Models\User;
use Tests\Support\HexagonIsolatedTestCase;

class CheckoutAccessV2Test extends HexagonIsolatedTestCase
{
    private function cart(): array
    {
        return [
            'example.com' => [
                'domain' => 'example.com',
                'extension' => '.com',
                'registration_price' => 260000,
            ],
        ];
    }

    private function orderData(): array
    {
        return [
            'customer_name' => 'Nguyen Van An',
            'customer_email' => 'buyer@gmail.com',
            'customer_phone' => '0900000000',
        ];
    }

    public function test_guest_can_still_open_cart(): void
    {
        $this->withSession(['hexagon_cart' => $this->cart()])
            ->get(route('cart.index'))
            ->assertOk();
    }

    public function test_guest_get_checkout_goes_to_customer_login_and_keeps_cart(): void
    {
        $this->withSession(['hexagon_cart' => $this->cart()])
            ->get(route('checkout.create'))
            ->assertRedirect(route('customer.login'));

        $this->assertSame(route('checkout.create'), session('url.intended'));
        $this->assertSame($this->cart(), session('hexagon_cart'));
    }

    public function test_guest_post_checkout_goes_to_login_and_intends_safe_get(): void
    {
        $this->withSession(['hexagon_cart' => $this->cart()])
            ->post(route('checkout.store'), $this->orderData())
            ->assertRedirect(route('customer.login'));

        $this->assertSame(route('checkout.create'), session('url.intended'));
        $this->assertSame($this->cart(), session('hexagon_cart'));
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_admin_get_checkout_returns_to_cart_with_explanation(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->withSession(['hexagon_cart' => $this->cart()])
            ->get(route('checkout.create'))
            ->assertRedirect(route('cart.index'))
            ->assertSessionHas('error');

        $this->assertSame($this->cart(), session('hexagon_cart'));
    }

    public function test_admin_post_checkout_cannot_create_order(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->withSession(['hexagon_cart' => $this->cart()])
            ->post(route('checkout.store'), $this->orderData())
            ->assertRedirect(route('cart.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('orders', 0);
        $this->assertSame($this->cart(), session('hexagon_cart'));
    }

    public function test_json_requests_get_correct_authentication_status(): void
    {
        $this->withHeader('Accept', 'application/json')
            ->get(route('checkout.create'))
            ->assertStatus(401);

        $this->actingAs(User::factory()->create(['is_admin' => true]))
            ->withHeader('Accept', 'application/json')
            ->post(route('checkout.store'), $this->orderData())
            ->assertStatus(403);

        $this->assertDatabaseCount('orders', 0);
    }
}
