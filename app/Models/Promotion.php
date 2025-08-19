<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',        // Tên khuyến mãi
        'code',        // Mã khuyến mãi
        'start_date',  // Cũ
        'end_date',    // Cũ
        'valid_from',  // Mới
        'valid_to',    // Mới
        'discount',
        'description',
        'expiry_date',
        'is_active'
    ];

    protected $casts = [
        'discount' => 'decimal:2',
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'valid_from' => 'date',
        'valid_to' => 'date',
        'expiry_date' => 'date',
    ];

    // Kiểm tra khuyến mãi có đang hoạt động
    public function getIsValidAttribute()
    {
        $now = now();
        // Ưu tiên valid_from/valid_to nếu có
        if ($this->valid_from && $this->valid_to) {
            return $now->between($this->valid_from, $this->valid_to) && $this->is_active;
        }
        // Fallback dùng start_date/end_date nếu có
        if ($this->start_date && $this->end_date) {
            return $now->between($this->start_date, $this->end_date) && $this->is_active;
        }
        // Fallback dùng expiry_date nếu có
        if ($this->expiry_date) {
            return $now->lte($this->expiry_date) && $this->is_active;
        }
        return $this->is_active; // Nếu không có ngày, chỉ kiểm tra is_active
    }

    // Quan hệ với sản phẩm
    public function products()
    {
        return $this->belongsToMany(Product::class, 'promotion_product');
    }

    // Tính giá sau khi áp dụng khuyến mãi
    public function getDiscountedPriceAttribute($originalPrice)
    {
        return $originalPrice - ($originalPrice * ($this->discount / 100));
    }
}
