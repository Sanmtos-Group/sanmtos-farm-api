<?php

namespace App\Policies;

use App\Models\Address;
use App\Models\User;
use App\Traits\Policy\AuthorizeAllActionToSuperAdmin;
use Illuminate\Auth\Access\Response;

class AddressPolicy
{
    use AuthorizeAllActionToSuperAdmin;

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
    public function view(User $user, Address $address): bool
    {
        $user_can_view_address = false;
        
        if($address->addressable_type  == User::class)
        {
            $user_can_view_address = $user_can_view_address || ($address->addressable_id == $user->id);
        }
        return $user_can_view_address;
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
    public function update(User $user, Address $address): bool
    {
        $user_can_update_address = false;
        
        if($address->addressable_type  == User::class)
        {
            $user_can_update_address = $user_can_update_address || ($address->addressable_id == $user->id);
        }
        return $user_can_update_address;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Address $address): bool
    {
        $user_can_delete_address = false;
        
        if($address->addressable_type  == User::class)
        {
            $user_can_delete_address = $user_can_delete_address || ($address->addressable_id == $user->id);
        }
        return $user_can_delete_address;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Address $address): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Address $address): bool
    {
        //
    }
}
