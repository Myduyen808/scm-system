<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\User;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $supplier = User::where('email', 'supplier@example.com')->first();

        $products = [
            [
                'name' => 'Laptop Dell XPS 13',
                'description' => 'Laptop cao cấp cho doanh nhân',
                'regular_price' => 25000000,
                'sale_price' => 22000000,
                'sku' => 'LAPTOP001',
                'stock_quantity' => 50,
                'supplier_id' => $supplier->id,
            ],
            [
                'name' => 'iPhone 15 Pro',
                'description' => 'Smartphone mới nhất của Apple',
                'regular_price' => 30000000,
                'sale_price' => null,
                'sku' => 'PHONE001',
                'stock_quantity' => 30,
                'supplier_id' => $supplier->id,
            ],
            [
                'name' => 'Samsung Galaxy Tab S9',
                'description' => 'Máy tính bảng Android cao cấp',
                'regular_price' => 15000000,
                'sale_price' => 13500000,
                'sku' => 'TABLET001',
                'stock_quantity' => 25,
                'supplier_id' => $supplier->id,
            ],
            [
                'name' => 'AirPods Pro 2',
                'description' => 'Tai nghe không dây chống ồn',
                'regular_price' => 6000000,
                'sale_price' => 5400000,
                'sku' => 'HEADPHONE001',
                'stock_quantity' => 100,
                'supplier_id' => $supplier->id,
            ],
            [
                'name' => 'MacBook Air M2',
                'description' => 'Laptop Apple siêu mỏng',
                'regular_price' => 28000000,
                'sale_price' => null,
                'sku' => 'LAPTOP002',
                'stock_quantity' => 20,
                'supplier_id' => $supplier->id,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
