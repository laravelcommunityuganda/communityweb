<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Job extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'description',
        'type',
        'company_name',
        'company_website',
        'location',
        'is_remote',
        'salary_min',
        'salary_max',
        'salary_currency',
        'required_skills',
        'deadline',
        'status',
        'is_featured',
        'views_count',
    ];

    protected $casts = [
        'is_remote' => 'boolean',
        'is_featured' => 'boolean',
        'salary_min' => 'integer',
        'salary_max' => 'integer',
        'required_skills' => 'array',
        'deadline' => 'datetime',
        'views_count' => 'integer',
    ];

    /**
     * Job types
     */
    const TYPE_FULL_TIME = 'full_time';
    const TYPE_PART_TIME = 'part_time';
    const TYPE_CONTRACT = 'contract';
    const TYPE_REMOTE = 'remote';
    const TYPE_INTERNSHIP = 'internship';
    const TYPE_FREELANCE = 'freelance';

    /**
     * Job statuses
     */
    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING = 'pending';
    const STATUS_PUBLISHED = 'published';
    const STATUS_CLOSED = 'closed';

    /**
     * Get the user who posted the job.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category of the job.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the applications for the job.
     */
    public function applications()
    {
        return $this->hasMany(JobApplication::class);
    }

    /**
     * Get the users who saved this job.
     */
    public function savedBy()
    {
        return $this->belongsToMany(User::class, 'job_user_saved');
    }

    /**
     * Scope for published jobs.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    /**
     * Scope for featured jobs.
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for remote jobs.
     */
    public function scopeRemote(Builder $query): Builder
    {
        return $query->where('is_remote', true);
    }

    /**
     * Search jobs.
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('company_name', 'like', "%{$search}%");
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
