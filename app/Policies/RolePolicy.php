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
        $user_can_update_role = $user->hasPermission('update role') && $role->name !== 'super-admin';

        // store owner can only edit store roles
        if($user->owns_a_store){
            $user_can_update_role = $user_can_update_role && $user->store->id == $role->store_id;
        }
        // sanmtos staff can only edit non store roles
        elseif($user->is_staff){
            $user_can_update_role = $user_can_update_role && is_null($role->store_id);
        }
        // store staff can only edit store roles
        else {
            $user_can_update_role = $user_can_update_role && $user->workingStores->where('store_id', $role->store_id)->count();

            if($user_can_update_role){
                $roles = $user->roles()->where('store_id', $role->store_id)->get();
                $staff_store_role_has_permission_to_update_role = false;
                foreach ($roles as $key => $value) {
                    if($value->permissions()->where('name', 'update role')->first()){
                        $staff_store_role_has_permission_to_update_role = true;
                        break;
                    }
                }
                $user_can_update_role = $user_can_update_role && $staff_store_role_has_permission_to_update_role;
            }

        }

        return $user_can_update_role;
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
