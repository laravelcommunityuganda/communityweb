<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MentorProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bio',
        'expertise_areas',
        'skills',
        'years_of_experience',
        'current_role',
        'company',
        'max_mentees',
        'current_mentees_count',
        'rating_average',
        'rating_count',
        'sessions_count',
        'is_available',
        'availability',
        'is_verified',
    ];

    protected $casts = [
        'expertise_areas' => 'array',
        'skills' => 'array',
        'availability' => 'array',
        'is_available' => 'boolean',
        'is_verified' => 'boolean',
        'rating_average' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mentorships()
    {
        return $this->hasMany(Mentorship::class, 'mentor_id');
    }

    public function activeMentorships()
    {
        return $this->mentorships()->where('status', 'active');
    }

    public function completedMentorships()
    {
        return $this->mentorships()->where('status', 'completed');
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeTopRated($query)
    {
        return $query->orderBy('rating_average', 'desc');
    }

    public function hasCapacity(): bool
    {
        return $this->current_mentees_count < $this->max_mentees;
    }

    public function getSpotsLeft(): int
    {
        return max(0, $this->max_mentees - $this->current_mentees_count);
    }

    public function updateRating(): void
    {
        $this->rating_count = MentorshipRating::where('mentor_id', $this->user_id)->count();
        $this->rating_average = MentorshipRating::where('mentor_id', $this->user_id)->avg('rating') ?? 0;
        $this->save();
    }

    public function incrementMentees(): void
    {
        $this->increment('current_mentees_count');
    }

    public function decrementMentees(): void
    {
        $this->decrement('current_mentees_count');
    }
}
