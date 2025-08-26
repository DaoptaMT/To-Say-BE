<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'recipient_email',
        'recipient_phone',
        'recipient_zalo',
        'message_type',
        'message_text',
        'is_anonymous',
        'approval_status',
        'rejection_reason',
        'approved_at',
        'receiver_name',
        'private_note',
        'sent_at'
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function mediaFiles()
    {
        return $this->hasMany(MediaFile::class);
    }

    public function logs()
    {
        return $this->hasMany(MessageLog::class);
    }

    public function replies()
    {
        return $this->hasMany(MessageReply::class);
    }

    public function scopePending($query)
    {
        return $query->where('approval_status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('approval_status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('approval_status', 'rejected');
    }

    public function scopeBySender($query, $userId)
    {
        return $query->where('sender_id', $userId);
    }
}
