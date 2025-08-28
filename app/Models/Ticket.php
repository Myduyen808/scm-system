<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\TicketReply; 

class Ticket extends Model
{
    protected $fillable = ['user_id', 'subject', 'description', 'status', 'assigned_to'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedEmployee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

        // Quan hệ tới user được assign
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
        public function replies()
    {
        return $this->hasMany(TicketReply::class);
        // TicketReply là model chứa các phản hồi, phải tạo trước
    }
}
