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
            $user_can_update_role = $user_can_update_role && $user->workStores()->where('store_id', $role->store_id)->count();

            if($user_can_update_role){
                $user_can_update_role = $user_can_update_role && $this->permissionIsGrantedByTheStoreInActionThroughRole($user, $role, 'update role'); 
            }

        }

        return $user_can_update_role;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Role $role): bool
    {
        $user_can_delete_role = $user->hasPermission('delete role') && $role->name !== 'super-admin';

        // store owner can only delete store roles
        if($user->owns_a_store){
            $user_can_delete_role = $user_can_delete_role && $user->store->id == $role->store_id;
        }
        // sanmtos staff can only delete non store roles
        elseif($user->is_staff){
            $user_can_delete_role = $user_can_delete_role && is_null($role->store_id);
        }
        // store staff can only delete store roles
        else {
            $user_can_delete_role = $user_can_delete_role && $user->workStores()->where('store_id', $role->store_id)->count();

            if($user_can_delete_role){
                $user_can_delete_role = $user_can_delete_role && $this->permissionIsGrantedByTheStoreInActionThroughRole($user, $role, 'delete role'); 
            }

        }

        return $user_can_delete_role;
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

    /**
     *  check if the current permission is granted by the store in action through the role 
     *  This ensure non permission for a store is used to permform unathourized action on another store 
     *  as a store has many staffs and those staffs can work for many stores
     * 
     * @param App\Models\User $user
     * @param App\Models\Role $role
     * @param string $permission
     */

    private function permissionIsGrantedByTheStoreInActionThroughRole($user,$role, $permission): bool 
    {
        $roles = $user->roles()->where('store_id', $role->store_id)->get();
        // check if the permission to update role is from the store that owns the role to be updated
        foreach ($roles as $key => $value) {
            if(!is_null($value->permissions()->where('name', $permission)->first())){
                return  true;
            }
        }
        return false;
    }
}
