<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task): bool
    {
        if ($user->id == $task->creator_id) {
            return true;
        } elseif ($task->project && $user->memberships->contains($task->project)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): bool
    {
        return $user->id == $task->creator_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): bool
    {
        return $user->id == $task->creator_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Task $task): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Task $task): bool
    {
        return false;
    }

    /**
     * Determine whether the authenticated user is authorized to assign a member to the given task.
     */
    public function assign(User $user, Task $task): bool
    {
        return $user->id == $task->creator_id;
    }

    /**
     * Determine whether the authenticated user is authorized to unassign a member from the given task.
     */
    public function unassign(User $user, Task $task): bool
    {
        return $user->id == $task->creator_id;
    }
}
