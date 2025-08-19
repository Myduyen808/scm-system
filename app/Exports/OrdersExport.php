<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrdersExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Order::select('order_number', 'total_amount', 'status', 'created_at')->get();
    }

    public function headings(): array
    {
        return ['Mã đơn', 'Tổng tiền', 'Trạng thái', 'Ngày tạo'];
    }
}
