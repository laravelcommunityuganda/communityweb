<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Message;
use Illuminate\Auth\Access\HandlesAuthorization;

class MessagePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any messages.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the message.
     */
    public function view(User $user, Message $message): bool
    {
        // Check if user is part of the conversation
        return $message->conversation->participants()->where('user_id', $user->id)->exists();
    }

    /**
     * Determine whether the user can create messages.
     */
    public function create(User $user): bool
    {
        return !$user->is_banned;
    }

    /**
     * Determine whether the user can delete the message.
     */
    public function delete(User $user, Message $message): bool
    {
        return $user->id === $message->user_id;
    }
}