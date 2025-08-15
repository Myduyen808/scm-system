<?php

namespace Database\Factories;

use App\Models\Promotion;
use Illuminate\Database\Eloquent\Factories\Factory;

class PromotionFactory extends Factory
{
    protected $model = Promotion::class;

    public function definition()
    {
        return [
            'name' => $this->faker->sentence(3),
            'discount' => $this->faker->randomFloat(2, 5, 50), // Giảm giá từ 5% đến 50%
            'start_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'end_date' => $this->faker->dateTimeBetween('now', '+2 months'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
