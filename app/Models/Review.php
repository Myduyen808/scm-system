<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'rating',
        'comment',
        'reviewed_at',
        'sentiment',
        'confidence'
    ];

    // Quan hệ với User nếu có
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Quan hệ với Product nếu có
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Accessor để lấy customer name
    public function getCustomerNameAttribute()
    {
        return $this->user->name ?? 'Khách hàng #' . $this->user_id ?? 'Khách hàng';
    }

    // Accessor để lấy content từ comment
    public function getContentAttribute()
    {
        return $this->comment;
    }
}
