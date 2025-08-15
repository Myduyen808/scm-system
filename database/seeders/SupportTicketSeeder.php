<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SupportTicket;
use App\Models\User;

class SupportTicketSeeder extends Seeder
{
    public function run()
    {
        $customer = User::where('email', 'customer@example.com')->first();

        $tickets = [
            [
                'user_id' => $customer->id,
                'subject' => 'Vấn đề về đơn hàng',
                'description' => 'Đơn hàng của tôi chưa được giao.',
                'status' => 'open',
            ],
            [
                'user_id' => $customer->id,
                'subject' => 'Hỏi về sản phẩm',
                'description' => 'Sản phẩm này có màu khác không?',
                'status' => 'in_progress',
            ],
        ];

        foreach ($tickets as $ticket) {
            SupportTicket::create($ticket);
        }
    }
}
