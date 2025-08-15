<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;

class OrderSeeder extends Seeder
{
    public function run()
    {
        Order::factory()->count(20)->create([
            'payment_status' => 'paid',
            'total_amount' => fake()->numberBetween(100000, 2000000),
            'status' => fake()->randomElement(['pending', 'processing', 'shipped', 'delivered']),
            'customer_id' => 3, // Customer User
        ]);
    }
}
