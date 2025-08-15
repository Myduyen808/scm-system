<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'discount', 'start_date', 'end_date', 'is_active'
    ];

    protected $casts = [
        'discount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Kiểm tra khuyến mãi có đang hoạt động
    public function getIsValidAttribute()
    {
        return now()->between($this->start_date, $this->end_date) && $this->is_active;
    }
}
