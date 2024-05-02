<?php 
namespace App\Traits\Policy; 

use App\Models\User;

trait AuthorizeAllActionToSuperAdmin
{
     /**
     * Perform pre-authorization checks.
     * 
     * @param App\Models\User $user
     * @param string $ability 
     * @return bool|null
     */
    public function before(User $user, string $ability): bool|null
    {

        if ($user->hasRole('super-admin')) {
            return true;
        }

        return null;
    }
}
