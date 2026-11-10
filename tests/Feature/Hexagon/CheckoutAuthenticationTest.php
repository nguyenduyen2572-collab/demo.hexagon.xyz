<?php

namespace Tests\Feature\Hexagon;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\Support\HexagonIsolatedTestCase;

class CheckoutAuthenticationTest extends HexagonIsolatedTestCase
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

    private function customerInfo(): array
    {
        return [
            'customer_name' => 'Nguyen Van An',
            'customer_email' => 'buyer@gmail.com',
            'customer_phone' => '0900000000',
        ];
    }

    public function test_guest_can_open_cart(): void
    {
        $this->withSession(['hexagon_cart' => $this->cart()])
            ->get(route('cart.index'))
            ->assertOk();
    }

    public function test_guest_must_sign_in_to_open_checkout(): void
    {
        $this->withSession(['hexagon_cart' => $this->cart()])
            ->get(route('checkout.create'))
            ->assertRedirect(route('customer.login'));

        $this->assertSame(route('checkout.create'), session('url.intended'));
        $this->assertSame($this->cart(), session('hexagon_cart'));
    }

    public function test_guest_cannot_submit_checkout_directly(): void
    {
        $this->withSession(['hexagon_cart' => $this->cart()])
            ->post(route('checkout.store'), $this->customerInfo())
            ->assertRedirect(route('customer.login'));

        $this->assertDatabaseCount('orders', 0);
        $this->assertSame($this->cart(), session('hexagon_cart'));
    }

    public function test_signed_in_customer_can_open_checkout(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)
            ->withSession(['hexagon_cart' => $this->cart()])
            ->get(route('checkout.create'))
            ->assertOk();
    }

    public function test_successful_login_returns_to_checkout_without_losing_cart(): void
    {
        $password = 'TestPassword1!';
        $user = User::factory()->create([
            'is_admin' => false,
            'password' => Hash::make($password),
        ]);

        $this->withSession(['hexagon_cart' => $this->cart()])
            ->get(route('checkout.create'))
            ->assertRedirect(route('customer.login'));

        $this->post(route('customer.login.store'), [
            'email' => $user->email,
            'password' => $password,
        ])->assertRedirect(route('checkout.create'));

        $this->assertAuthenticatedAs($user);
        $this->assertSame($this->cart(), session('hexagon_cart'));
    }

    public function test_admin_cannot_checkout_as_customer(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)
            ->withSession(['hexagon_cart' => $this->cart()])
            ->get(route('checkout.create'))
            ->assertRedirect(route('cart.index'));

        $this->post(route('checkout.store'), $this->customerInfo())
            ->assertRedirect(route('cart.index'));

        $this->assertDatabaseCount('orders', 0);
    }
}
