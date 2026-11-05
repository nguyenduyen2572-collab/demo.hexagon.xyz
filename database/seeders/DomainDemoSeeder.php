<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DomainDemoSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // 1. Bảng giá tên miền giả lập
        DB::table('domain_prices')->upsert([
            [
                'extension' => '.com',
                'registration_price' => 299000,
                'renewal_price' => 350000,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'extension' => '.net',
                'registration_price' => 320000,
                'renewal_price' => 370000,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'extension' => '.vn',
                'registration_price' => 750000,
                'renewal_price' => 450000,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'extension' => '.com.vn',
                'registration_price' => 650000,
                'renewal_price' => 350000,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ], ['extension'], [
            'registration_price',
            'renewal_price',
            'updated_at'
        ]);

        // 2. Trạng thái tên miền giả lập
        DB::table('mock_domain_statuses')->upsert([
            [
                'domain' => 'hexagonshop.com',
                'is_available' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'domain' => 'example.com',
                'is_available' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'domain' => 'hexagonshop.vn',
                'is_available' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'domain' => 'demohexagon.net',
                'is_available' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ], ['domain'], [
            'is_available',
            'updated_at'
        ]);
    }
}
