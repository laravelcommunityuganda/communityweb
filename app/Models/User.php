<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'avatar',
        'cover_image',
        'role',
        'reputation',
        'is_verified',
        'is_banned',
        'ban_reason',
        'banned_until',
        'provider',
        'provider_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_verified' => 'boolean',
        'is_banned' => 'boolean',
        'banned_until' => 'datetime',
    ];

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'followers', 'following_id', 'follower_id')
            ->withTimestamps();
    }

    public function following()
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'following_id')
            ->withTimestamps();
    }

    public function blockedUsers()
    {
        return $this->belongsToMany(User::class, 'blocked_users', 'user_id', 'blocked_user_id')
            ->withTimestamps();
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function resources()
    {
        return $this->hasMany(Resource::class);
    }

    public function jobApplications()
    {
        return $this->hasMany(JobApplication::class);
    }

    public function postedJobs()
    {
        return $this->hasMany(Job::class, 'user_id');
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function attendingEvents()
    {
        return $this->belongsToMany(Event::class, 'event_attendees', 'user_id', 'event_id')
            ->withPivot(['status', 'ticket_number', 'is_checked_in', 'checked_in_at'])
            ->withTimestamps();
    }

    public function mentorProfile()
    {
        return $this->hasOne(MentorProfile::class);
    }

    public function mentorshipsAsMentor()
    {
        return $this->hasMany(Mentorship::class, 'mentor_id');
    }

    public function mentorshipsAsMentee()
    {
        return $this->hasMany(Mentorship::class, 'mentee_id');
    }

    public function conversations()
    {
        return $this->belongsToMany(Conversation::class, 'conversation_participants')
            ->withPivot(['last_read_at', 'is_muted'])
            ->withTimestamps();
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function userNotifications()
    {
        return $this->hasMany(UserNotification::class);
    }

    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'user_badges')
            ->withPivot('earned_at')
            ->withTimestamps();
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function reputationHistory()
    {
        return $this->hasMany(ReputationHistory::class);
    }

    public function savedPosts()
    {
        return $this->belongsToMany(Post::class, 'post_bookmarks')->withTimestamps();
    }

    public function savedJobs()
    {
        return $this->belongsToMany(Job::class, 'saved_jobs')->withTimestamps();
    }

    public function savedResources()
    {
        return $this->belongsToMany(Resource::class, 'resource_bookmarks')->withTimestamps();
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin' || $this->hasRole('admin');
    }

    public function isModerator(): bool
    {
        return $this->role === 'moderator' || $this->hasRole('moderator') || $this->isAdmin();
    }

    public function isVerifiedDeveloper(): bool
    {
        return $this->is_verified || $this->role === 'verified_developer' || $this->hasRole('verified_developer');
    }

    public function isRecruiter(): bool
    {
        return $this->role === 'recruiter' || $this->hasRole('recruiter');
    }

    public function isBanned(): bool
    {
        if ($this->is_banned) {
            return true;
        }

        if ($this->banned_until && $this->banned_until->isFuture()) {
            return true;
        }

        return false;
    }

    public function addReputation(int $points, string $action, $source = null): void
    {
        $this->increment('reputation', $points);

        ReputationHistory::create([
            'user_id' => $this->id,
            'points' => $points,
            'action' => $action,
            'source_type' => $source ? get_class($source) : null,
            'source_id' => $source?->id,
        ]);
    }

    public function subtractReputation(int $points, string $action, $source = null): void
    {
        $this->decrement('reputation', $points);

        ReputationHistory::create([
            'user_id' => $this->id,
            'points' => -$points,
            'action' => $action,
            'source_type' => $source ? get_class($source) : null,
            'source_id' => $source?->id,
        ]);
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }

    public function getProfileUrlAttribute(): string
    {
        return route('profiles.show', $this->username);
    }
}
