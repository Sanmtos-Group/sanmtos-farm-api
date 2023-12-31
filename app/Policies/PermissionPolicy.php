<?php

namespace App\Policies;

use App\Models\Permission;
use App\Models\User;
use App\Traits\Policy\AuthorizeAllActionToSuperAdmin;
use Illuminate\Auth\Access\Response;

class PermissionPolicy
{
    use AuthorizeAllActionToSuperAdmin;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user=null): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user=null, Permission $permission): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Permission $permission): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Permission $permission): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Permission $permission): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Permission $permission): bool
    {
        //
    }

    /**
     * Determine whether the user can grant the model.
     */
    public function grant(User $user, Permission $permission): bool
    {
       return $user->hasPermission('grant permission') && $permission->is_assignable;
    }

    /**
     * Determine whether the user can sync all permissions.
     */
    public function sync(User $user=null): bool
    {
       return true;
    }
}
