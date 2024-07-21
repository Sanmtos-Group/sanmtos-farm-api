<?php

namespace App\Models;

use App\Services\CloudinaryService;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\Features;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;
use LucasDotVin\Soulbscription\Models\Concerns\HasSubscriptions;
class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasUuids;
    use HasProfilePhoto;
    use HasSubscriptions;
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
        'owns_a_store',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['roles', 'preference'];

    /**
     * User name attribute
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->first_name.' '.$this->last_name,
        );
    }

    /** Update the user's profile photo.
     * @override Laravel\Jetstream\HasProfilePhoto public function updateProfilePhote(UploadedFile $photo)
     * 
     * @param  \Illuminate\Http\UploadedFile  $photo
     * @return void
     */
    public function updateProfilePhoto(UploadedFile $photo)
    {

        tap($this->profile_photo_path, function ($previous) use ($photo) {
            // $this->forceFill([
            //     'profile_photo_path' => $photo->storePublicly(
            //         'profile-photos', ['disk' => $this->profilePhotoDisk()]
            //     ),
            // ])->save();

            $options = [
                'overlayImageURL' => null, //
                'thumbnail' => null, /// null or ['width'=>700, 'height'=>700]
                'dimensions' => ['width'=>400, 'height'=>400], // null or ['width'=>700, 'height'=>700]
                'roundCorners' => 0,
            ];

            // upload to cloudinary
            $uploaded_photo = CloudinaryService::uploadImage($photo, $folder='profile-photos/', $options);
            
            $this->forceFill([
                'profile_photo_path' =>  $uploaded_photo->getSecurePath()
            ])->save();

            if ($previous) 
            {
                // Storage::disk($this->profilePhotoDisk())->delete($previous);
                CloudinaryService::destroy($previous);
            }
        });
    }

    /**
     * Delete the user's profile photo.
     * @override Laravel\Jetstream\HasProfilePhoto public function deleteProfilePhoto()
     * 
     * @return void
     */
    public function deleteProfilePhoto()
    {

        if (! Features::managesProfilePhotos()) 
        {
            return;
        }

        if (is_null($this->profile_photo_path)) 
        {
            return;
        }

        // Storage::disk($this->profilePhotoDisk())->delete($this->profile_photo_path);
        CloudinaryService::destroy($this->profile_photo_path);


        $this->forceFill([
            'profile_photo_path' => null,
        ])->save();
    }
    

    /**
     * Get the URL to the user's profile photo.
     *
     *  @override Laravel\Jetstream\HasProfilePhoto public function profilePhotoUrl()
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function profilePhotoUrl(): Attribute
    {
        return Attribute::get(function (): string {
            return $this->profile_photo_path
                    // ? Storage::disk($this->profilePhotoDisk())->url($this->profile_photo_path)
                    ?  $this->profile_photo_path
                    : $this->defaultProfilePhotoUrl();
        });
    }


    /**
     * Get all of the model's addresses.
     */
    public function addresses() : MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    /**
     * Get the user country, currecy's image.
     */
    public function preference()
    {
        return $this->morphOne(Preference::class, 'preferenceable');
    }

    /**
     * The notifications preferences that belongs to the user (vendor).
     */
    public function notificationPreferences()
    {
        return $this->belongsToMany(NotificationPreference::class)->using(NotificationPreferenceUser::class);

    }

    /**
     * Get all of the model's addresses.
     */
    public function orders() : HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the store that the users owns.
     */
    public function store(): HasOne
    {
        return $this->hasOne(Store::class);
    }

     /**
     * The stores that the user works for.
     */
    public function workStores(): BelongsToMany
    {
        return $this->belongsToMany(Store::class, StoreUser::class);
    }

    /**
     * The cart items for the user.
     */
    public function cartItems(): hasMany
    {
        return $this->hasMany(Cart::class);
    }

    /**
     * Get the user verification codes 
     * 
     */
    public function verificationCodes(): hasMany 
    {
        return $this->hasMany(VerificationCode::class);
    }

    /**
     * The roles that belong to the user.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)->using(RoleUser::class);
    }

    /**
     * Get the user's coupon usages.
     */
    public function couponUsages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    /**
     * Scope a query to only include users that owns a certain store
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mix  $values
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRoles($query, ...$values)
    {
        $query->withWhereHas('roles', function($query) use($values){
            $query->whereIn('roles.id', $values);

            foreach ($values as $key => $value) {
                $query->orWhere('roles.name','like',"%".$value."%");
            }
                
        });
        
        return $query; 
    }


    /**
     * Scope a query to only include users of particular store they work for.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mix  $values
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWorkStores($query, ...$values)
    {
        $query->withWhereHas('workStores', function($query) use($values){
            $query->whereIn('stores.id', $values);

            foreach ($values as $key => $value) {
                $query->orWhere('stores.name','like',"%".$value."%")
                ->orWhere('stores.slug','like',"%".$value."%");
            }
                
        });
        
        return $query; 
    }


    /**
     * Scope a query to only include users that owns a certain store
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mix  $values
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStore($query, ...$values)
    {
        $query->withWhereHas('store', function($query) use($values){
            $query->whereIn('id', $values);

            foreach ($values as $key => $value) {
                $query->orWhere('name','like',"%".$value."%")
                ->orWhere('slug','like',"%".$value."%");
            }
                
        });
        
        return $query; 
    }


    /**
     * Determine if a user owns a store
     */
    protected function ownsAStore(): Attribute
    {
        return Attribute::make(
            get: fn () => !empty($this->store),
        );
    }

   

    /**
     * Assign roles to user 
     */

    public function assignRole($role)
    {
        if($this->hasRole($role))
        {
           return true;
        }

        if(!is_null($the_role=$this->findRole($role))){
            $this->roles()->attach($the_role->id,['created_at'=>now()]);
            return true;
        }

        $new_role = Role::create(['name' => $role]);
        if(is_null($new_role))
        {
            return false;
        }
        
        $this->roles()->attach($new_role->id,['created_at'=>now()]);

        return true;
    }

      /**
     * Revoke role to user 
     */
    public function revokeRole($role)
    {
        if(!$this->hasRole($role))
        {
           return true;
        }

        if(!is_null($the_role=$this->findRole($role))){
            $this->roles()->detach($the_role->id);
            return true;
        }

        return true;
    }

    /**
     * @param \App\Models\Role || null
     * 
     * @return \App\Models\Role || null
     */
    public function findRole($role) : ?Role
    {
        $role_type = gettype($role);

        switch ($role_type) {
            case 'string':
                if(Str::isUuid($role))
                    return Role::find($role);
                else
                    return Role::where('id', $role)->orWhere('name',$role)->first();
                break;
            case 'object':
                return get_class($role) == 'App\Models\Role'? Role::where('id', $role->id)->first() :null;
                break;
            default:
                return null;
                break;
        }
        return null;
    }

    /**
     * check if the user has a given role.
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
                break;
            default:
                return false;
                break;
        }
        return false;
    }

    /**
     * check if the user has any given roles.
     *
     * @param optional \App\Models\Role||uuid||string||array  $role1, $role2, $role3...
     * @return bool
     */
    public function hasAnyRole(): bool
    {
        $args = func_get_args();  // Get all arguments as an array

        foreach ($args as $index => $value) {

            if(is_array($value)){
                $flatten_values = flattenArray($value);
                foreach ($flatten_values as $key => $flatten_value) {
                    if($this->hasRole($flatten_value))
                        return true;
                }
               continue;
            }

            if($this->hasRole($value))
                return true;
        }

        return false;
    }

    /**
     * Get all of the permissions for the user through roles.
     *
     * @return Illuminate\Support\Collection
     */
    public function permissions(): Collection
    {
        $user = User::with('roles.permissions')->find($this->id);
        $permissions = $user->roles->flatMap(function ($role) {
            return [$role->name => $role->permissions->toArray()];
        });
        return $permissions;
    }

    /**
     * check if the user has a permission through it roles.
     *
     * @param optional \App\Models\Permission||uuid||string||array  $permission
     * @return bool
     */
    public function hasPermission($permission): bool
    {
        $permission_type = gettype($permission);

        switch ($permission_type) {
            case 'string':
                return in_array($permission, $this->permissions()->dot()->all());
                break;

            case 'object':
                return get_class($permission) == 'App\Models\Permission'? in_array($permission->id, $this->permissions()->dot()->all()) :false;
                break;

            default:
                return false;
                break;
        }
        return false;
    }

    /**
     * check if the user has any given permission through it role.
     *
     * @param optional \App\Models\Permission||uuid||string||array  $permission1, $permission2, $permission3...
     * @return bool
     */
    public function hasAnyPermission(): bool
    {
        $args = func_get_args();  // Get all arguments as an array

        foreach ($args as $index => $value) {

            if(is_array($value)){
                $flatten_values = flattenArray($value);
                foreach ($flatten_values as $key => $flatten_value) {
                    if($this->hasPermission($flatten_value))
                        return true;
                }
               continue;
            }

            if($this->hasPermission($value))
                return true;
        }

        return false;
    }

    /**
     * Generator OTP for the  user
     * 
     * @param App\Carbon\Carbon||null $expire_at 
     * 
     * @return int $OTP;
     */
    function generateOTP(Carbon $expire_at=null) : int
    {
        # Check if user have an existing OTP
        $verification_code = $this->verificationCodes()->first();

        $now = Carbon::now();

        if(!is_null($verification_code))
        {
            if ($now->isBefore($verification_code->expire_at))
            {
                return $verification_code->otp;
            }

            // delete user all verification codes
            $this->verificationCodes()->delete(); 
        }

        $expire_at = $expire_at?? $now->addHours();

        $verification_code = $this->verificationCodes()->create([
            'otp' => rand(123456, 999999),
            'expire_at' => $expire_at
        ]);

        return $verification_code->otp;
    }
}
