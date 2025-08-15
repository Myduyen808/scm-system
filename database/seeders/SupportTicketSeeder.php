<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SupportTicket;

class SupportTicketSeeder extends Seeder
{
    public function run()
    {
        // Chỉ seed nếu bảng SupportTickets chưa có dữ liệu
        if (SupportTicket::count() == 0) {
            SupportTicket::create([
                'subject' => 'Hỗ trợ đơn hàng',
                'description' => 'Đơn hàng bị lỗi, cần hỗ trợ.',
                'status' => 'open',
            ]);

            SupportTicket::factory()->count(4)->create();
        }
    }
}
