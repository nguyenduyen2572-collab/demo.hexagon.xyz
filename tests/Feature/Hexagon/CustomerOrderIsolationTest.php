<?php

namespace Tests\Feature\Hexagon;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\Support\HexagonIsolatedTestCase;

class CustomerOrderIsolationTest extends HexagonIsolatedTestCase
{
    public function test_customer_cannot_read_another_customers_order(): void
    {
        $owner = User::factory()->create();
        $visitor = User::factory()->create();
        $code = (string) Str::uuid();

        DB::table('orders')->insert([
            'order_code' => $code,
            'user_id' => $owner->id,
            'customer_name' => $owner->name,
            'customer_email' => $owner->email,
            'customer_phone' => '0900000000',
            'total_amount' => 260000,
            'status' => 'pending_demo',
            'is_mock' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($visitor)
            ->get(route('customer.account.orders.show', ['code' => $code]))
            ->assertNotFound();
    }
}
