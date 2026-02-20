<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MentorshipRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'mentorship_id',
        'mentor_id',
        'mentee_id',
        'rating',
        'review',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    public function mentorship()
    {
        return $this->belongsTo(Mentorship::class);
    }

    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public function mentee()
    {
        return $this->belongsTo(User::class, 'mentee_id');
    }

    public function scopeHighRated($query)
    {
        return $query->where('rating', '>=', 4);
    }

    public function scopeLowRated($query)
    {
        return $query->where('rating', '<=', 2);
    }
}
