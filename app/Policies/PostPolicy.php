<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Post;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any posts.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the post.
     */
    public function view(?User $user, Post $post): bool
    {
        if ($post->status === 'published') {
            return true;
        }

        return $user && ($user->id === $post->user_id || $user->hasRole(['admin', 'moderator']));
    }

    /**
     * Determine whether the user can create posts.
     */
    public function create(User $user): bool
    {
        return !$user->is_banned;
    }

    /**
     * Determine whether the user can update the post.
     */
    public function update(User $user, Post $post): bool
    {
        if ($user->hasRole(['admin', 'moderator'])) {
            return true;
        }

        return $user->id === $post->user_id;
    }

    /**
     * Determine whether the user can delete the post.
     */
    public function delete(User $user, Post $post): bool
    {
        if ($user->hasRole(['admin', 'moderator'])) {
            return true;
        }

        return $user->id === $post->user_id;
    }

    /**
     * Determine whether the user can restore the post.
     */
    public function restore(User $user, Post $post): bool
    {
        return $user->hasRole(['admin', 'moderator']);
    }

    /**
     * Determine whether the user can permanently delete the post.
     */
    public function forceDelete(User $user, Post $post): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can accept answer for the post.
     */
    public function acceptAnswer(User $user, Post $post): bool
    {
        return $user->id === $post->user_id || $user->hasRole(['admin', 'moderator']);
    }

    /**
     * Determine whether the user can moderate posts.
     */
    public function moderate(User $user): bool
    {
        return $user->hasRole(['admin', 'moderator']);
    }
}