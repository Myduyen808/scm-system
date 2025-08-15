<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Promotion;

class PromotionSeeder extends Seeder
{
    public function run()
    {
        // Chỉ seed nếu bảng Promotions chưa có dữ liệu
        if (Promotion::count() == 0) {
            Promotion::create([
                'name' => 'Giảm giá mùa hè',
                'discount' => 20.00,
                'start_date' => now(),
                'end_date' => now()->addMonth(),
            ]);

            Promotion::factory()->count(4)->create();
        }
    }
}
