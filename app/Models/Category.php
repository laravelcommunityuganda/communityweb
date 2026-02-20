<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
    ];

    /**
     * Get the posts for the category.
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Get the jobs for the category.
     */
    public function jobs()
    {
        return $this->hasMany(Job::class);
    }

    /**
     * Get the events for the category.
     */
    public function events()
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Get the resources for the category.
     */
    public function resources()
    {
        return $this->hasMany(Resource::class);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
