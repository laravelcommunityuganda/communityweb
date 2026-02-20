<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bio',
        'title',
        'company',
        'location',
        'github_url',
        'portfolio_url',
        'linkedin_url',
        'twitter_url',
        'website_url',
        'skills',
        'social_links',
        'is_available_for_work',
        'is_available_for_mentoring',
        'show_email',
        'show_location',
        'notification_preferences',
    ];

    protected $casts = [
        'skills' => 'array',
        'social_links' => 'array',
        'notification_preferences' => 'array',
        'is_available_for_work' => 'boolean',
        'is_available_for_mentoring' => 'boolean',
        'show_email' => 'boolean',
        'show_location' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getSkillsListAttribute(): array
    {
        return $this->skills ?? [];
    }

    public function hasSkill(string $skill): bool
    {
        return in_array($skill, $this->skills ?? []);
    }

    public function addSkill(string $skill): void
    {
        $skills = $this->skills ?? [];
        if (!in_array($skill, $skills)) {
            $skills[] = $skill;
            $this->skills = $skills;
            $this->save();
        }
    }

    public function removeSkill(string $skill): void
    {
        $skills = $this->skills ?? [];
        $this->skills = array_values(array_diff($skills, [$skill]));
        $this->save();
    }
}
