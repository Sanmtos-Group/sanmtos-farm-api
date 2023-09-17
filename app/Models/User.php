<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasUuids;
    use HasProfilePhoto;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        // 'name',
        'first_name',
        'last_name',
        'gender',
        'dialing_code',
        'phone_number',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

     /**
     * Get the store that the users owns.
     */
    public function store(): HasOne
    {
        return $this->hasOne(Store::class);
    }

    /**
     * The roles that belong to the user.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)->using(RoleUser::class);
    }

    /**
     * check if the user has a role.
     * 
     * @param \App\Models\Role||uuid||string $role
     * @return bool
     */
    public function hasRole($role): bool
    {
        $role_type = gettype($role);

        switch ($role_type) {
            case 'string':
                if(Str::isUuid($role))
                    return !is_null($this->roles()->where('roles.id', $role)->first());
                else 
                    return !is_null($this->roles()->where('roles.id', $role)->orWhere('roles.name', $role)->first());
                break;
            case 'object':
                return get_class($role) == 'App\Models\Role'? !is_null($this->roles()->where('roles.id', $role->id)->first()) :false;
            default:
                return false;
                break;
        }
        return false;
    }

    /**
     * Get all of the deployments for the project.
     */
    public function permissions(): HasManyThrough
    {
        return $this->throughRoles()->hasPermissions();
    }
}
