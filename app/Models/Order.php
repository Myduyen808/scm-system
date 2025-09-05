<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number', 'customer_id', 'total_amount', 'status',
        'shipping_address', 'payment_method', 'payment_status',
        'shipping_status', 'tracking_number', 'delivered_at', 'shipping_note',
        'shipping_name',   //  thêm dòng này
        'shipping_phone',  //  thêm dòng này
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'delivered_at' => 'datetime',
    ];

    // Relationships
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'customer_id'); // Hoặc thay 'customer_id' bằng cột thực tế nếu khác
    }
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    // Generate order number
    public static function generateOrderNumber()
    {
        return 'ORD' . date('Ymd') . str_pad(self::count() + 1, 4, '0', STR_PAD_LEFT);
    }

    public function updateStatus($status)
    {
        $allowedStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        if (in_array($status, $allowedStatuses)) {
            $this->status = $status;
            if ($status === 'delivered') {
                $this->delivered_at = now();
                $this->shipping_status = 'delivered';
            } elseif ($status === 'shipped') {
                $this->shipping_status = 'shipped';
            } else {
                $this->shipping_status = $status;
            }
            $this->save();
        }
    }

    // Kiểm tra trạng thái giao hàng
    public function getShippingStatusTextAttribute()
    {
        switch ($this->shipping_status) {
            case 'pending':
                return 'Đang chờ xử lý';
            case 'processing':
                return 'Đang chuẩn bị hàng';
            case 'shipped':
                return 'Đã giao cho đơn vị vận chuyển';
            case 'delivered':
                return 'Đã giao thành công';
            case 'cancelled':
                return 'Đã hủy';
            default:
                return 'Không xác định';
        }
    }
}
