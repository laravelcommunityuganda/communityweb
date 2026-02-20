<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Comment;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any comments.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the comment.
     */
    public function view(?User $user, Comment $comment): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create comments.
     */
    public function create(User $user): bool
    {
        return !$user->is_banned;
    }

    /**
     * Determine whether the user can update the comment.
     */
    public function update(User $user, Comment $comment): bool
    {
        if ($user->hasRole(['admin', 'moderator'])) {
            return true;
        }

        return $user->id === $comment->user_id;
    }

    /**
     * Determine whether the user can delete the comment.
     */
    public function delete(User $user, Comment $comment): bool
    {
        if ($user->hasRole(['admin', 'moderator'])) {
            return true;
        }

        return $user->id === $comment->user_id;
    }
}