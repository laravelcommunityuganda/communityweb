<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Resource;
use Illuminate\Auth\Access\HandlesAuthorization;

class ResourcePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any resources.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the resource.
     */
    public function view(?User $user, Resource $resource): bool
    {
        if ($resource->status === 'approved') {
            return true;
        }

        return $user && ($user->id === $resource->user_id || $user->hasRole(['admin', 'moderator']));
    }

    /**
     * Determine whether the user can create resources.
     */
    public function create(User $user): bool
    {
        return !$user->is_banned;
    }

    /**
     * Determine whether the user can update the resource.
     */
    public function update(User $user, Resource $resource): bool
    {
        if ($user->hasRole(['admin', 'moderator'])) {
            return true;
        }

        return $user->id === $resource->user_id;
    }

    /**
     * Determine whether the user can delete the resource.
     */
    public function delete(User $user, Resource $resource): bool
    {
        if ($user->hasRole(['admin', 'moderator'])) {
            return true;
        }

        return $user->id === $resource->user_id;
    }

    /**
     * Determine whether the user can approve resources.
     */
    public function approve(User $user): bool
    {
        return $user->hasRole(['admin', 'moderator']);
    }
}