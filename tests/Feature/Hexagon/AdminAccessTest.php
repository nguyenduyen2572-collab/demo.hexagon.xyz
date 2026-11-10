<?php

namespace Tests\Feature\Hexagon;

use App\Models\User;
use Tests\Support\HexagonIsolatedTestCase;

class AdminAccessTest extends HexagonIsolatedTestCase
{
    public function test_guest_cannot_open_admin_orders(): void
    {
        $this->get(route('admin.orders.index'))
            ->assertRedirect(route('login'));
    }

    public function test_regular_customer_is_forbidden_from_admin_orders(): void
    {
        $customer = User::factory()->create();
        $customer->is_admin = false;
        $customer->save();

        $this->actingAs($customer)
            ->get(route('admin.orders.index'))
            ->assertForbidden();
    }

    public function test_admin_can_open_orders(): void
    {
        $admin = User::factory()->create();
        $admin->is_admin = true;
        $admin->save();

        $this->actingAs($admin, 'admin')
            ->get(route('admin.orders.index'))
            ->assertOk();
    }
}
