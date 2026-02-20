<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MentorshipSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'mentorship_request_id',
        'scheduled_at',
        'duration_minutes',
        'notes',
        'meeting_url',
        'status',
        'rating',
        'review',
        'completed_at',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'duration_minutes' => 'integer',
        'rating' => 'integer',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the mentorship request.
     */
    public function mentorshipRequest()
    {
        return $this->belongsTo(MentorshipRequest::class);
    }
}
