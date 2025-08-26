<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'message_id',
        'channel',
        'status',
        'sent_at',
    ];

    public function message()
    {
        return $this->belongsTo(Message::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
