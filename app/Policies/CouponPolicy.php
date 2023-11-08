<?php

namespace App\Policies;

use App\Models\Coupon;
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
    public function view(User $user, Coupon $coupon): bool
    {
        //
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
    public function update(User $user, Coupon $coupon): bool
    {
        return $user->owns_a_store || $user->hasPermission('update coupon') || true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Coupon $coupon): bool
    {
        return Auth::user() == $user->owns_a_store || $user->owns_a_store || $user->hasPermission('delete coupon') || true;
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
