<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call(RolePermissionSeeder::class);
        $this->call(ProductSeeder::class);
        $this->call(OrderSeeder::class);
        $this->call(PromotionSeeder::class);
        $this->call(SupportTicketSeeder::class);

        $supplier = User::whereHas('roles', function ($query) {
        $query->where('name', 'supplier');
        })->first();

        Request::create([
            'request_number' => 'REQ-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
            'supplier_id' => $supplier->id,
            'details' => 'Yêu cầu nhập 100 sản phẩm A',
            'status' => 'pending',
        ]);

        $customer = User::whereHas('roles', function ($query) {
            $query->where('name', 'customer');
        })->first();
        $supplier = User::whereHas('roles', function ($query) {
            $query->where('name', 'supplier');
        })->first();

        Product::create([
            'name' => 'Sản phẩm A',
            'sku' => 'SP-A-1000',
            'regular_price' => 500000,
            'sale_price' => 450000,
            'stock_quantity' => 100,
            'supplier_id' => $supplier->id,
            'is_active' => true,
        ]);

        $order = Order::create([
            'user_id' => $customer->id,
            'order_number' => 'ORD-' . time(),
            'total_amount' => 450000,
            'status' => 'completed',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => 1,
            'quantity' => 1,
            'price' => 450000,
        ]);
    }
}
