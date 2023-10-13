<?php

namespace App\Policies;

use App\Models\Promo;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use App\Traits\Policy\AuthorizeAllActionToSuperAdmin;
use App\Traits\Policy\StorePermissionableViaRole;

class PromoPolicy
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
    public function view(User $user, Promo $promo): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return  $user->owns_a_store || $user->hasPermission('create promo');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Promo $promo): bool
    {
        return $user->owns_a_store || $user->hasPermission('update promo') || true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Promo $promo): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Promo $promo): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Promo $promo): bool
    {
        //
    }
}
