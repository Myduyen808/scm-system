<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestModel extends Model
{
    protected $table = 'request_models'; // Đảm bảo tên bảng khớp với migration
    protected $fillable = [
        'supplier_id',
        'product_id',
        'quantity',
        'description',
        'status',
        'note', // Ghi chú từ nhân viên
        'employee_feedback', // Phản hồi từ nhà cung cấp
        'request_number', // Mã yêu cầu (nếu có)
    ];

    public function supplier()
    {
        return $this->belongsTo(User::class, 'supplier_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
