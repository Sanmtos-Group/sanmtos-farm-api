<?php

namespace App\Policies;

use App\Models\StoreInvitation;
use App\Models\User;
use App\Traits\Policy\AuthorizeAllActionToSuperAdmin;
use Illuminate\Auth\Access\Response;

class StoreInvitationPolicy
{
    use AuthorizeAllActionToSuperAdmin;
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $this->user->owns_a_store;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, StoreInvitation $storeInvitation): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, StoreInvitation $storeInvitation): bool
    {
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, StoreInvitation $storeInvitation): bool
    {
        return true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, StoreInvitation $storeInvitation): bool
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, StoreInvitation $storeInvitation): bool
    {
        return true;
    }
}
