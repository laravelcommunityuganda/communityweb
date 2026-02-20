<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'conversation_id',
        'user_id',
        'content',
        'file_path',
        'file_name',
        'file_size',
        'file_type',
        'read_at',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'read_at' => 'datetime',
    ];

    /**
     * Get the conversation.
     */
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Get the user who sent the message.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
