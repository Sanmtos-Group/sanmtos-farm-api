<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;
use App\Traits\Policy\AuthorizeAllActionToSuperAdmin;
use App\Traits\Policy\StorePermissionableViaRole;
use Illuminate\Auth\Access\Response;

class RolePolicy
{

    use AuthorizeAllActionToSuperAdmin;
    use StorePermissionableViaRole;
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
    public function view(User $user=null, Role $role): bool
    {
        return true;
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
     * Determine whether the user can assign role to a user
     * 
     * @param App\Models\User $user
     * @param App\Models\Role $role;
     * @param App\Models\User $to_user;
     */
    public function assign(User $user, Role $role, ?User $to_user): bool 
    {

        $user_can_assign_role = $user->hasPermission('assign role') && $role->name !== 'super-admin';


        if(!is_null($to_user)){
            $user_can_assign_role = $user_can_assign_role && (
                $to_user->is_staff || $to_user->workStores()->where('store_id', $role->store_id)->count()
            );
        }

        
        if($user->owns_a_store){
            // store owner can only assign store roles
            $user_can_assign_role = $user_can_assign_role && $user->store->id == $role->store_id;

        } // sanmtos staff can only assign non store roles
        elseif($user->is_staff){
            $user_can_assign_role = $user_can_assign_role && is_null($role->store_id);
         
        }
        // store staff can only assign store roles
        else {
            $user_can_assign_role = $user_can_assign_role && $user->workStores()->where('store_id', $role->store_id)->count();

            if($user_can_assign_role){
                $user_can_assign_role = $user_can_assign_role && $this->permissionIsGrantedByTheStoreInActionThroughRole($user, $role, 'assign role'); 
            }

        }
        return $user_can_assign_role;
    }

    /**
     * Determine whether the user can remove role to a user
     * 
     * @param App\Models\User $user
     * @param App\Models\Role $role;
     * @param App\Models\User $to_user;
     */
    public function remove(User $user, Role $role, ?User $to_user): bool 
    {
        return true;
    }

    
}
