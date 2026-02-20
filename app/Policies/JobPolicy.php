<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Job;
use Illuminate\Auth\Access\HandlesAuthorization;

class JobPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any jobs.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the job.
     */
    public function view(?User $user, Job $job): bool
    {
        if ($job->status === 'published') {
            return true;
        }

        return $user && ($user->id === $job->user_id || $user->hasRole(['admin', 'moderator']));
    }

    /**
     * Determine whether the user can create jobs.
     */
    public function create(User $user): bool
    {
        return !$user->is_banned && $user->hasAnyRole(['admin', 'moderator', 'verified_developer', 'recruiter']);
    }

    /**
     * Determine whether the user can update the job.
     */
    public function update(User $user, Job $job): bool
    {
        if ($user->hasRole(['admin', 'moderator'])) {
            return true;
        }

        return $user->id === $job->user_id;
    }

    /**
     * Determine whether the user can delete the job.
     */
    public function delete(User $user, Job $job): bool
    {
        if ($user->hasRole(['admin', 'moderator'])) {
            return true;
        }

        return $user->id === $job->user_id;
    }

    /**
     * Determine whether the user can approve jobs.
     */
    public function approve(User $user): bool
    {
        return $user->hasRole(['admin', 'moderator']);
    }

    /**
     * Determine whether the user can feature jobs.
     */
    public function feature(User $user): bool
    {
        return $user->hasRole('admin');
    }
}