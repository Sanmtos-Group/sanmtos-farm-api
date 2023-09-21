<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;
use App\Traits\Policy\AuthorizeAllActionToSuperAdmin;
use Illuminate\Auth\Access\Response;

class RolePolicy
{

    use AuthorizeAllActionToSuperAdmin;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Role $role): bool
    {
        return $user->hasPermission('read role');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('create role');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Role $role): bool
    {
        // $user_can_update_role = $user->hasPermission('update role') && $role->name !== 'super-admin';

        // if($user->owns_a_store){
        //     $user_can_update_role = $user_can_update_role && $user->store->id == $role->store_id;
        // }
        // elseif(!is_null($role->store_id)){

        //     $filtered = $user->permissions()->where('name', 'update role');

        // }

        // return $user_can_update_role;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Role $role): bool
    {
        return $user->hasPermission('delete role');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Role $role): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Role $role): bool
    {
        //
    }
}
