<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Promotion;

class PromotionSeeder extends Seeder
{
    public function run()
    {
        $promotions = [
            [
                'name' => 'Khuyến mãi hè',
                'discount' => 20.00,
                'start_date' => now()->subDays(5),
                'end_date' => now()->addDays(10),
                'is_active' => true,
            ],
            [
                'name' => 'Black Friday',
                'discount' => 50.00,
                'start_date' => now()->addDays(20),
                'end_date' => now()->addDays(25),
                'is_active' => true,
            ],
        ];

        foreach ($promotions as $promotion) {
            Promotion::create($promotion);
        }
    }
}
