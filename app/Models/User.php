<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Product;
use App\Models\Order;
use App\Models\Cart;
use App\Models\Address;
use App\Models\SupportTicket;
use App\Models\Review;
use App\Models\Notification;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'avatar',
        'is_active',
        'last_notification_check',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'last_notification_check' => 'datetime',
    ];

    /**
     * Sản phẩm do user cung cấp (cho supplier)
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'supplier_id');
    }

    /**
     * Đơn hàng của khách hàng
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    /**
     * Giỏ hàng của user
     */
    public function cartItems()
    {
        return $this->hasMany(Cart::class, 'user_id');
    }

    /**
     * Địa chỉ của user
     */
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    /**
     * Phiếu hỗ trợ (support tickets)
     */
    public function supportTickets()
    {
        return $this->hasMany(SupportTicket::class);
    }

    /**
     * Đánh giá của user
     */
    public function reviews()
    {
        return $this->hasMany(Review::class, 'user_id');
    }

    /**
     * Thông báo của user
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Thông báo chưa đọc
     */
    public function unreadNotifications()
    {
        return $this->hasMany(Notification::class)->where('is_read', false);
    }

    /**
     * Lấy số lượng thông báo chưa đọc
     */
    public function getUnreadNotificationsCountAttribute()
    {
        return $this->unreadNotifications()->count();
    }

    /**
     * Sản phẩm yêu thích (favorites)
     */
    public function favorites(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'favorites', 'user_id', 'product_id');
    }
}
