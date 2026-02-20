<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MentorshipRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'mentorship_id',
        'mentee_id',
        'message',
        'goals',
        'preferred_schedule',
        'status',
        'accepted_at',
        'sessions_count',
    ];

    protected $casts = [
        'goals' => 'array',
        'accepted_at' => 'datetime',
        'sessions_count' => 'integer',
    ];

    /**
     * Get the mentorship.
     */
    public function mentorship()
    {
        return $this->belongsTo(Mentorship::class);
    }

    /**
     * Get the mentee.
     */
    public function mentee()
    {
        return $this->belongsTo(User::class, 'mentee_id');
    }

    /**
     * Get the sessions.
     */
    public function sessions()
    {
        return $this->hasMany(MentorshipSession::class, 'mentorship_request_id');
    }
}