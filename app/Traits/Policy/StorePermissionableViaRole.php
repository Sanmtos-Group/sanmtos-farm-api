<?php 
namespace App\Traits\Policy; 

use App\Models\User;
use App\Models\Role;

trait StorePermissionableViaRole
{
     /**
     *  check if the current permission is granted by the store in action via  store role 
     *  This ensures a granted permission for a store via store role is not used to perform unathourized action on another store 
     *  Since a store has many staffs and those staffs can work for many other stores
     * 
     * @param App\Models\User $user
     * @param App\Models\Role $role
     * @param string $permission
     */

    private function permissionIsGrantedByTheStoreInActionThroughRole(User $user, Role $role, $permission): bool 
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
