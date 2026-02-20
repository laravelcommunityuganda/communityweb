<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mentorship extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'company',
        'expertise',
        'experience_years',
        'bio',
        'availability',
        'hourly_rate',
        'rating',
        'sessions_count',
        'is_available',
        'status',
    ];

    protected $casts = [
        'expertise' => 'array',
        'experience_years' => 'integer',
        'hourly_rate' => 'decimal:2',
        'rating' => 'decimal:1',
        'sessions_count' => 'integer',
        'is_available' => 'boolean',
    ];

    /**
     * Mentorship statuses
     */
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    /**
     * Get the user (mentor).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the mentorship requests.
     */
    public function requests()
    {
        return $this->hasMany(MentorshipRequest::class);
    }
}
