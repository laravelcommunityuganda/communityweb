<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'content',
        'type',
        'status',
        'is_solved',
        'solved_at',
        'views_count',
        'upvotes_count',
        'downvotes_count',
        'comments_count',
        'bookmarks_count',
    ];

    protected $casts = [
        'is_solved' => 'boolean',
        'solved_at' => 'datetime',
        'views_count' => 'integer',
        'upvotes_count' => 'integer',
        'downvotes_count' => 'integer',
        'comments_count' => 'integer',
        'bookmarks_count' => 'integer',
    ];

    /**
     * Post types
     */
    const TYPE_QUESTION = 'question';
    const TYPE_DISCUSSION = 'discussion';
    const TYPE_TUTORIAL = 'tutorial';

    /**
     * Post statuses
     */
    const STATUS_DRAFT = 'draft';
    const STATUS_PUBLISHED = 'published';
    const STATUS_ARCHIVED = 'archived';

    /**
     * Get the user who wrote the post.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category of the post.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the tags for the post.
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'post_tag');
    }

    /**
     * Get the comments for the post.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get the votes for the post.
     */
    public function votes()
    {
        return $this->morphMany(Vote::class, 'votable');
    }

    /**
     * Get the users who bookmarked this post.
     */
    public function bookmarkedBy()
    {
        return $this->belongsToMany(User::class, 'post_user_bookmark');
    }

    /**
     * Scope for published posts.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    /**
     * Scope for questions.
     */
    public function scopeQuestions(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_QUESTION);
    }

    /**
     * Scope for solved posts.
     */
    public function scopeSolved(Builder $query): Builder
    {
        return $query->where('is_solved', true);
    }

    /**
     * Scope for unsolved posts.
     */
    public function scopeUnsolved(Builder $query): Builder
    {
        return $query->where('is_solved', false);
    }

    /**
     * Search posts.
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
                ->orWhere('content', 'like', "%{$search}%");
        });
    }

    /**
     * Get excerpt attribute.
     */
    public function getExcerptAttribute(): string
    {
        return \Str::limit(strip_tags($this->content), 200);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
