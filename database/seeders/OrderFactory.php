<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        return [
            'payment_status' => 'paid',
            'total_amount' => $this->faker->numberBetween(100000, 2000000),
            'status' => $this->faker->randomElement(['pending', 'processing', 'shipped', 'delivered']),
            'customer_id' => 3, // Gắn với Customer User
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => now(),
        ];
    }
}
