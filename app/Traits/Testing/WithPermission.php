<?php
namespace App\Traits\Testing; 
use App\Models\Permission;

trait WithPermission {

    /**
     * The permission instance.
     *
     * @var \App\Models\Permission
     */
    protected $permission;

    /**
     * Setup up a new permission instance.
     *
     * @return \App\Models\Permission
     */
    protected function setUpPermission(): void
    {
        $this->permission = Permission::factory()->create();
    }

    /**
     * @return \App\Models\Permission
     */
    protected function makePermission($permission_data = null): Permission
    {
        return is_array($permission_data) ? Permission::factory()->make($permission_data) : Permission::factory()->make() ;   
    }

     /**
     * Get the permission instance for a given data.
     *
     * @param  array<string ,*>|null  $permission_data
     * 
     * @return \App\Models\Permission
     */
    public function permission($permission_data = null ): Permission
    {
        $permission = is_array($permission_data) ? Permission::firstOrCreate(Permission::factory()->make($permission_data)->toArray()) : Permission::first();
        return $permission ?? Permission::factory()->create();
    }

    /**
     * Get a trashed permission data.
     *
     * @return \App\Models\Permission
     */
    public function permissionTrashed(): Permission 
    {
        $permission_trashed = Permission::onlyTrashed()->get()->first();
        if($permission_trashed)
            return  $permission_trashed;
            
        $permission_trashed = $this->permission();
        $permission_trashed->delete();
        return $permission_trashed;
    }

}