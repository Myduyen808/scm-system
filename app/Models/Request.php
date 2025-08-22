<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    protected $fillable = ['request_number', 'supplier_id', 'details', 'status'];

    public function supplier()
    {
        return $this->belongsTo(User::class, 'supplier_id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
