<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'message_id',
        'file_type',
        'file_path',
        'file_name',
        'mime_type',
        'duration',
        'size',
    ];

    public function message()
    {
        return $this->belongsTo(Message::class);
    }

    public function scopeImages($query)
    {
        return $query->where('file_type', 'image');
    }
}
