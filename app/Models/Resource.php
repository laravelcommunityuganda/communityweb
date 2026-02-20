<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Resource extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'description',
        'type',
        'url',
        'file_path',
        'file_size',
        'code',
        'language',
        'rating',
        'views_count',
        'downloads_count',
        'status',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'rating' => 'decimal:1',
        'views_count' => 'integer',
        'downloads_count' => 'integer',
    ];

    /**
     * Resource types
     */
    const TYPE_PDF = 'pdf';
    const TYPE_GITHUB = 'github';
    const TYPE_YOUTUBE = 'youtube';
    const TYPE_SNIPPET = 'snippet';
    const TYPE_BOILERPLATE = 'boilerplate';

    /**
     * Resource statuses
     */
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    /**
     * Get the user who shared the resource.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category of the resource.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the ratings for the resource.
     */
    public function ratings()
    {
        return $this->hasMany(ResourceRating::class);
    }

    /**
     * Get the users who bookmarked this resource.
     */
    public function bookmarkedBy()
    {
        return $this->belongsToMany(User::class, 'resource_user_bookmark');
    }

    /**
     * Scope for approved resources.
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Search resources.
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
