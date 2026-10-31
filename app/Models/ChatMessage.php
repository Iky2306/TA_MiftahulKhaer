<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;
    
    protected $table = 'chat_messages';
    protected $guarded = [];

    public function karyawan()
    {
        return $this->belongsTo(\App\Models\Karyawan::class, 'nik', 'nik');
    }

    public function replyTo()
    {
        return $this->belongsTo(ChatMessage::class, 'reply_to_id');
    }
}
