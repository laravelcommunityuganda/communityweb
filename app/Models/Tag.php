<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'usage_count',
    ];

    protected $casts = [
        'usage_count' => 'integer',
    ];

    /**
     * Get the posts for the tag.
     */
    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_tag');
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
