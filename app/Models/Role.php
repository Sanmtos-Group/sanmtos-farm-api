<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;
    use HasUuids;

    
    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => \App\Events\Role\RoleCreated::class,
        'updated' => \App\Events\Role\RoleUpdated::class,
        'deleted' => \App\Events\Role\RoleDeleted::class,
    ];
    
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'store_id',
    ];


    /**
     * The users with the role.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

     /**
     * The store that the role belongs .
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * The permissions that belong to the role.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class)->using(PermissionRole::class);

    }

    /**
     * Grant a permission to the role
     * 
     * @param \App\Models\Permission||uuid||string $role
     * @return bool
     */
    public function grantPermission($permission): bool
    {
        $permission_type = gettype($permission);

        switch ($permission_type) {
            case 'string':
                if(Str::isUuid($role)){
                   $permission = Permission::find($permission);
                }
                else {
                    $permission = Permission::where('name', $permission)->first();
                }
                break;

            case 'object':
                if(get_class($permission) == 'App\Models\Permission'){
                    $permission = Permission::find($permission->id); 
                }
                else{
                    $permission = null;
                }
                break;

            default:
                $permission = null;
                break;
        }

        if(is_null($permission))
            return false;

        if(is_null($this->permissions()->where('permissions.id', $permission->id)->first())){
            $this->permissions()->save($permission);
        }

        return true;
    }

}
