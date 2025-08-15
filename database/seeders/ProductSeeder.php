<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run()
    {
        if (Product::count() == 0) { // Chỉ seed nếu chưa có dữ liệu
            Product::create([
                'name' => 'Sản phẩm 1',
                'sku' => 'PROD001',
                'stock_quantity' => 100,
                'price' => 500000,
            ]);

            Product::factory()->count(9)->create();
        }
    }
}
