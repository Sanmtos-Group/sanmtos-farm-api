<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use App\Traits\Policy\AuthorizeAllActionToSuperAdmin;

class TaskPolicy
{
    use AuthorizeAllActionToSuperAdmin;
    
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
        return $user->hasAnyRole(['store-admin', 'admin']) 
                || $user->hasPermission('read task')
                || $user->id == $task->assignee_user_id
                || $user->id == $task->assigner_user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['store-admin', 'admin']) || $user->owns_a_store || $user->hasPermission('create task');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): bool
    {
        return $user->id == $task->assignee_user_id
                || $user->id == $task->assigner_user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): bool
    {
        return $user->id == $task->assigner_user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Task $task): bool
    {
        return $user->hasAnyRole(['store-admin', 'admin']) 
                || $user->id == $task->assigner_user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Task $task): bool
    {
        return false;
    }
}
