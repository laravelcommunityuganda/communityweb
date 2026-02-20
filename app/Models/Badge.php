<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'type',
        'points',
        'icon',
        'color',
        'criteria',
        'is_active',
    ];

    protected $casts = [
        'points' => 'integer',
        'criteria' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Badge types
     */
    const TYPE_ACHIEVEMENT = 'achievement';
    const TYPE_SKILL = 'skill';
    const TYPE_SPECIAL = 'special';

    /**
     * Get the users who have this badge.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_badges')
            ->withPivot('earned_at')
            ->withTimestamps();
    }
}
