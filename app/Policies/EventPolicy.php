<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Event;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any events.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the event.
     */
    public function view(?User $user, Event $event): bool
    {
        if ($event->status === 'published') {
            return true;
        }

        return $user && ($user->id === $event->user_id || $user->hasRole(['admin', 'moderator']));
    }

    /**
     * Determine whether the user can create events.
     */
    public function create(User $user): bool
    {
        return !$user->is_banned;
    }

    /**
     * Determine whether the user can update the event.
     */
    public function update(User $user, Event $event): bool
    {
        if ($user->hasRole(['admin', 'moderator'])) {
            return true;
        }

        return $user->id === $event->user_id;
    }

    /**
     * Determine whether the user can delete the event.
     */
    public function delete(User $user, Event $event): bool
    {
        if ($user->hasRole(['admin', 'moderator'])) {
            return true;
        }

        return $user->id === $event->user_id;
    }
}