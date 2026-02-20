<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'description',
        'type',
        'format',
        'venue_name',
        'venue_address',
        'venue_city',
        'venue_map_url',
        'online_url',
        'start_date',
        'end_date',
        'capacity',
        'is_free',
        'price',
        'cover_image',
        'status',
        'views_count',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'capacity' => 'integer',
        'is_free' => 'boolean',
        'price' => 'decimal:2',
        'views_count' => 'integer',
    ];

    /**
     * Event types
     */
    const TYPE_MEETUP = 'meetup';
    const TYPE_WORKSHOP = 'workshop';
    const TYPE_CONFERENCE = 'conference';
    const TYPE_WEBINAR = 'webinar';
    const TYPE_HACKATHON = 'hackathon';

    /**
     * Event formats
     */
    const FORMAT_PHYSICAL = 'physical';
    const FORMAT_ONLINE = 'online';
    const FORMAT_HYBRID = 'hybrid';

    /**
     * Event statuses
     */
    const STATUS_DRAFT = 'draft';
    const STATUS_PUBLISHED = 'published';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_ENDED = 'ended';

    /**
     * Get the user who created the event.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category of the event.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the attendees for the event.
     */
    public function attendees()
    {
        return $this->belongsToMany(User::class, 'event_attendees')
            ->withPivot('status', 'registered_at')
            ->withTimestamps();
    }

    /**
     * Scope for published events.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    /**
     * Scope for upcoming events.
     */
    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('start_date', '>=', now());
    }

    /**
     * Scope for past events.
     */
    public function scopePast(Builder $query): Builder
    {
        return $query->where('end_date', '<', now());
    }

    /**
     * Search events.
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        });
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
