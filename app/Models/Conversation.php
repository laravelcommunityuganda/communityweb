<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Conversation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'name',
    ];

    /**
     * Conversation types
     */
    const TYPE_PRIVATE = 'private';
    const TYPE_GROUP = 'group';

    /**
     * Get the participants of the conversation.
     */
    public function participants()
    {
        return $this->belongsToMany(User::class, 'conversation_user')
            ->withPivot('is_admin')
            ->withTimestamps();
    }

    /**
     * Get the messages of the conversation.
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Scope for conversations with last message.
     */
    public function scopeWithLastMessage($query)
    {
        return $query->with(['messages' => function ($q) {
            $q->latest()->limit(1);
        }]);
    }
}
