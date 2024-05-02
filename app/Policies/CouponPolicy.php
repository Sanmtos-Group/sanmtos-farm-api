<?php

namespace App\Policies;

use App\Models\Coupon;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use App\Traits\Policy\AuthorizeAllActionToSuperAdmin;
use App\Traits\Policy\StorePermissionableViaRole;
use Illuminate\Support\Facades\Auth;

class CouponPolicy
{
    use AuthorizeAllActionToSuperAdmin;
    use StorePermissionableViaRole;

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
    public function view(User $user=null, Coupon $coupon): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return  $user->owns_a_store || $user->hasPermission('create coupon');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Coupon $coupon, Role $role): bool
    {
        $can_update_coupon = $user->hasPermission('update coupon');

        // store owner can only delete store coupon
        if($user->owns_a_store){
            $can_update_coupon = $can_update_coupon && $user->store->id == $coupon->store_id;
        }
        // sanmtos staff can only delete non store coupon
        elseif($user->is_staff){
            $can_update_coupon = $can_update_coupon && is_null($coupon->store_id);
        }
        // store staff can only delete store roles
        else {
            $can_update_coupon = $can_update_coupon && $user->workStores()->where('store_id', $coupon->store_id)->count();

            if($can_update_coupon){
                $can_update_coupon = $can_update_coupon && $this->permissionIsGrantedByTheStoreInActionThroughRole($user, $role, 'delete coupon');
            }

        }

        return $can_update_coupon;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Coupon $coupon, Role $role): bool
    {
        $can_delete_coupon = $user->hasPermission('delete coupon');

        // store owner can only delete store coupon
        if($user->owns_a_store){
            $can_delete_coupon = $can_delete_coupon && $user->store->id == $coupon->store_id;
        }
        // sanmtos staff can only delete non store coupon
        elseif($user->is_staff){
            $can_delete_coupon = $can_delete_coupon && is_null($coupon->store_id);
        }
        // store staff can only delete store roles
        else {
            $can_delete_coupon = $can_delete_coupon && $user->workStores()->where('store_id', $coupon->store_id)->count();

            if($can_delete_coupon){
                $can_delete_coupon = $can_delete_coupon && $this->permissionIsGrantedByTheStoreInActionThroughRole($user, $role, 'delete coupon');
            }

        }

        return $can_delete_coupon;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Coupon $coupon): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Coupon $coupon): bool
    {
        return false;
    }
}
