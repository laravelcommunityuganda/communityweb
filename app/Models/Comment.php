<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'post_id',
        'parent_id',
        'content',
        'votes_count',
        'is_best_answer',
        'is_edited',
        'edited_at',
    ];

    protected $casts = [
        'votes_count' => 'integer',
        'is_best_answer' => 'boolean',
        'is_edited' => 'boolean',
        'edited_at' => 'datetime',
    ];

    /**
     * Get the user who wrote the comment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the post that the comment belongs to.
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Get the parent comment.
     */
    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * Get the replies for the comment.
     */
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    /**
     * Get the votes for the comment.
     */
    public function votes()
    {
        return $this->morphMany(Vote::class, 'votable');
    }
}
