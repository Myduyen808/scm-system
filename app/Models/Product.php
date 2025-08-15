<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'regular_price', 'sale_price',
        'image', 'sku', 'stock_quantity', 'is_active', 'supplier_id'
    ];

    protected $casts = [
        'regular_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function supplier()
    {
        return $this->belongsTo(User::class, 'supplier_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cartItems()
    {
        return $this->hasMany(Cart::class);
    }

    // Accessors
    public function getCurrentPriceAttribute()
    {
        return $this->sale_price ?? $this->regular_price;
    }

    public function getIsOnSaleAttribute()
    {
        return !is_null($this->sale_price);
    }

    public function promotions()
    {
        return $this->belongsToMany(Promotion::class, 'promotion_product');
    }
}
