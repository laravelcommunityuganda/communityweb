<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sender_id',
        'type',
        'title',
        'message',
        'data',
        'related_type',
        'related_id',
        'is_read',
        'read_at',
        'is_emailed',
        'emailed_at',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'is_emailed' => 'boolean',
        'read_at' => 'datetime',
        'emailed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function related()
    {
        return $this->morphTo();
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function isRead(): bool
    {
        return $this->is_read;
    }

    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    public function markAsEmailed(): void
    {
        if (!$this->is_emailed) {
            $this->update([
                'is_emailed' => true,
                'emailed_at' => now(),
            ]);
        }
    }
}
