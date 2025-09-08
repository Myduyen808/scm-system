<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestModel extends Model
{
    use HasFactory;

    protected $table = 'requests';

    protected $fillable = [
        'supplier_id',
        'employee_id',
        'product_id',
        'quantity',
        'description',
        'employee_note',
        'note_from_supplier',
        'employee_feedback',
        'status',
        'request_number',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    // Relationships
    public function supplier()
    {
        return $this->belongsTo(User::class, 'supplier_id');
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // Accessors & Mutators
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'accepted' => 'success',
            'rejected' => 'danger',
            default => 'secondary'
        };
    }

    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'pending' => 'Đang chờ',
            'accepted' => 'Đã chấp nhận',
            'rejected' => 'Đã từ chối',
            default => 'Không xác định'
        };
    }
    public function replies()
    {
        return $this->hasMany(RequestReply::class, 'request_id');
    }
}
