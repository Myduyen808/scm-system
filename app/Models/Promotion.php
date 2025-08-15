<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'start_date', 'end_date', 'discount', 'is_active'];

    protected $casts = [
        'discount' => 'decimal:2',
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // Kiểm tra khuyến mãi có đang hoạt động
    public function getIsValidAttribute()
    {
        return now()->between($this->start_date, $this->end_date) && $this->is_active;
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'promotion_product');
    }
}
