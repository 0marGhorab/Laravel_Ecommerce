<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        Coupon::firstOrCreate(
            ['code' => 'SAVE10'],
            [
                'type' => 'percentage',
                'value' => 10,
                'min_order_amount' => 50,
                'max_uses' => null,
                'uses_per_user' => 1,
                'starts_at' => null,
                'expires_at' => null,
                'active' => true,
                'description' => '10% off orders over $50',
            ]
        );

        Coupon::firstOrCreate(
            ['code' => 'FLAT20'],
            [
                'type' => 'fixed',
                'value' => 20,
                'min_order_amount' => 75,
                'max_uses' => 100,
                'uses_per_user' => null,
                'starts_at' => null,
                'expires_at' => null,
                'active' => true,
                'description' => '$20 off orders over $75',
            ]
        );

        Coupon::firstOrCreate(
            ['code' => 'WELCOME15'],
            [
                'type' => 'percentage',
                'value' => 15,
                'min_order_amount' => null,
                'max_uses' => null,
                'uses_per_user' => 1,
                'starts_at' => null,
                'expires_at' => null,
                'active' => true,
                'description' => '15% off first order',
            ]
        );
    }
}
