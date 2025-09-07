<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Product extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name', 'description', 'regular_price', 'sale_price',
        'image', 'sku', 'stock_quantity', 'is_active', 'supplier_id', 'current_price', 'is_approved'
    ];

    protected $casts = [
        'regular_price' => 'decimal:2',
        'sale_price'    => 'decimal:2',
        'is_active'     => 'boolean',
    ];

    public function supplier()
    {
        return $this->belongsTo(User::class, 'supplier_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_items')->withPivot('quantity', 'price');
    }

    public function cartItems()
    {
        return $this->hasMany(Cart::class);
    }

    public function promotions()
    {
        return $this->belongsToMany(Promotion::class, 'promotion_product');
    }

    // Thêm mối quan hệ ngược lại với User qua bảng favorites
    public function users()
    {
        return $this->belongsToMany(User::class, 'favorites', 'product_id', 'user_id');
    }

    // Accessor cho giá hiện tại, ưu tiên khuyến mãi nếu có
    public function getCurrentPriceAttribute()
    {
        $basePrice = $this->sale_price ?? $this->regular_price ?? 0;

        // Kiểm tra khuyến mãi hợp lệ
        $activePromotion = $this->promotions->first(function ($promotion) {
            return $promotion->isValid; // Sử dụng isValid như thuộc tính
        });

        if ($activePromotion) {
            return $activePromotion->getDiscountedPriceAttribute($basePrice);
        }

        return $basePrice;
    }

    // Scope để chỉ lấy sản phẩm approved và active
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true)->where('is_active', true);
    }

    public function getIsOnSaleAttribute()
    {
        return !is_null($this->sale_price);
    }

    public function scopeForSupplier($query, $supplierId)
    {
        return $query->where('supplier_id', $supplierId);
    }

    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }

    // Cái này là bắt buộc khi dùng LogsActivity
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll() // log hết các field fillable
            ->useLogName('product')
            ->setDescriptionForEvent(fn(string $eventName) => "Product has been {$eventName}");
    }
}
