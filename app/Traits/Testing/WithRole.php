<?php
namespace App\Traits\Testing; 
use App\Models\Role;

trait WithRole {

    /**
     * The role instance.
     *
     * @var \App\Models\Role
     */
    protected $role;

    /**
     * Setup up a new role instance.
     *
     * @return \App\Models\Role
     */
    protected function setUpRole(): void
    {
        $this->role = Role::factory()->create();
    }

    /**
     * @return \App\Models\Role
     */
    protected function makeRole($role_data = null): Role
    {
        return is_array($role_data) ? Role::factory()->make($role_data) : Role::factory()->make() ;   
    }

     /**
     * Get the role instance for a given data.
     *
     * @param  array<string ,*>|null  $role_data
     * 
     * @return \App\Models\Role
     */
    public function role($role_data = null ): Role
    {
        $role = is_array($role_data) ? Role::firstOrCreate(Role::factory()->make($role_data)->toArray()) : Role::first();
        return $role ?? Role::factory()->create();
    }

    /**
     * Get a trashed role data.
     *
     * @return \App\Models\Role
     */
    public function roleTrashed(): Role 
    {
        $role_trashed = Role::onlyTrashed()->get()->first();
        if($role_trashed)
            return  $role_trashed;
            
        $role_trashed = $this->role();
        $role_trashed->delete();
        return $role_trashed;
    }

}