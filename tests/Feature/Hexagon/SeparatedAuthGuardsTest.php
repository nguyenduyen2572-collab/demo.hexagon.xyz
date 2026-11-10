<?php

declare(strict_types=1);

namespace Tests\Feature\Hexagon;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tests\Support\HexagonIsolatedTestCase;

final class SeparatedAuthGuardsTest extends HexagonIsolatedTestCase
{
    public function test_admin_login_only_authenticates_admin_guard(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'email' => 'test-admin-separate@gmail.com',
            'password' => bcrypt('StrongAdminPass!123'),
        ]);

        $this->post(route('admin.login.submit'), [
            'email' => $admin->email,
            'password' => 'StrongAdminPass!123',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertTrue(Auth::guard('admin')->check());
        $this->assertFalse(Auth::guard('web')->check());
        $this->get(route('admin.dashboard'))->assertOk();
    }

    public function test_customer_cannot_log_in_using_admin_page(): void
    {
        $customer = User::factory()->create([
            'is_admin' => false,
            'email' => 'test-customer-not-admin@gmail.com',
            'password' => bcrypt('StrongCustomer!123'),
        ]);

        $this->post(route('admin.login.submit'), [
            'email' => $customer->email,
            'password' => 'StrongCustomer!123',
        ])->assertSessionHasErrors('email');

        $this->assertFalse(Auth::guard('admin')->check());
    }

    public function test_admin_cannot_log_in_using_customer_page(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'email' => 'admin-not-customer@gmail.com',
            'password' => bcrypt('StrongAdmin!123'),
        ]);

        $this->post('/customer/login', [
            'email' => $admin->email,
            'password' => 'StrongAdmin!123',
        ])->assertSessionHasErrors('email');

        $this->assertFalse(Auth::guard('web')->check());
    }

    public function test_customer_only_session_cannot_open_admin_routes(): void
    {
        $customer = User::factory()->create(['is_admin' => false]);

        $this->actingAs($customer, 'web')
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_both_guards_can_coexist_and_admin_logout_keeps_customer(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $customer = User::factory()->create(['is_admin' => false]);

        $this->actingAs($customer, 'web');
        $this->actingAs($admin, 'admin');

        $this->get(route('admin.dashboard'))->assertOk();
        $this->post(route('admin.logout'))->assertRedirect(route('login'));

        $this->assertFalse(Auth::guard('admin')->check());
        $this->assertTrue(Auth::guard('web')->check());
        $this->assertSame($customer->id, Auth::guard('web')->id());
    }

    public function test_real_admin_and_customer_logins_keep_cart_and_both_guards(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'email' => 'admin-dual-session@gmail.com',
            'password' => bcrypt('StrongAdmin!123'),
        ]);
        $customer = User::factory()->create([
            'is_admin' => false,
            'email' => 'customer-dual-session@gmail.com',
            'password' => bcrypt('StrongCustomer!123'),
        ]);
        $cart = ['example.com' => [
            'domain' => 'example.com',
            'extension' => '.com',
            'registration_price' => 260000,
        ]];

        $this->withSession(['hexagon_cart' => $cart]);
        $this->post(route('admin.login.submit'), [
            'email' => $admin->email,
            'password' => 'StrongAdmin!123',
        ])->assertRedirect(route('admin.dashboard'));

        $this->post('/customer/login', [
            'email' => $customer->email,
            'password' => 'StrongCustomer!123',
        ])->assertRedirect();

        $this->assertSame($admin->id, Auth::guard('admin')->id());
        $this->assertSame($customer->id, Auth::guard('web')->id());
        $this->assertSame($cart, session('hexagon_cart'));
    }

    public function test_customer_logout_keeps_admin_guard(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $customer = User::factory()->create(['is_admin' => false]);
        $this->actingAs($customer, 'web');
        $this->actingAs($admin, 'admin');

        $this->post(route('customer.logout'))->assertRedirect();
        $this->assertFalse(Auth::guard('web')->check());
        $this->assertTrue(Auth::guard('admin')->check());
    }
}
