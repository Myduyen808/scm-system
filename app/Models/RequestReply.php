<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestReply extends Model
{
    protected $table = 'request_replies';

    // Cho phép ghi đè các cột này
    protected $fillable = ['request_id', 'user_id', 'message'];

    // Mỗi reply thuộc về 1 request
    public function request()
    {
        return $this->belongsTo(RequestModel::class, 'request_id');
    }

    // Mỗi reply thuộc về 1 user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
