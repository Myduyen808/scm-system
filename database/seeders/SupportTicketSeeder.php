<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SupportTicket;

class SupportTicketSeeder extends Seeder
{
    public function run()
    {
        SupportTicket::create([
            'title' => 'Vấn đề giao hàng',
            'description' => 'Đơn hàng bị chậm trễ',
            'status' => 'open',
            'user_id' => 1, // ID của user (ví dụ Admin)
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        SupportTicket::create([
            'title' => 'Hỏi về sản phẩm',
            'description' => 'Sản phẩm bị lỗi',
            'status' => 'open',
            'user_id' => 2, // ID của user khác
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
