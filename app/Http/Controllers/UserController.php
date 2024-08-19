<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\User;
use App\Models\Role;
use App\Http\Resources\AddressResource;
use App\Http\Resources\NotificationPreferenceResource;
use App\Http\Resources\OrderResource;
use App\Http\Resources\PreferenceResource;
use App\Http\Resources\RoleResource;
use App\Http\Resources\UserResource;
use App\Http\Requests\UpdateAddressRequest;
use App\Http\Requests\UpdatePreferenceRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\StorePreferenceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;
class UserController extends Controller
{

    /**
     * Create the controller instance.
     */
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = QueryBuilder::for(User::class)
        ->defaultSort('created_at')
        ->allowedSorts(
            'first_name',
            'last_name',
            'gender',
            'phone_number',
            AllowedSort::custom('recent', new \App\Models\Sorts\LatestSort()),
            AllowedSort::custom('oldest', new \App\Models\Sorts\OldestSort()),
        )
        ->allowedFilters([
            'first_name',
            'last_name',
            'gender',
            'phone_number',
            AllowedFilter::exact('store_id'),
            AllowedFilter::exact('category_id'),
            AllowedFilter::scope('min_price'),
            AllowedFilter::scope('max_price'),
            AllowedFilter::scope('price_between'),
            AllowedFilter::scope('category'),
            AllowedFilter::scope('store'),
            AllowedFilter::scope('recent'),
            AllowedFilter::scope('roles'),
            AllowedFilter::callback('rolesId', function($query,$value){
                $query->whereHas('roles', function($query) use($value){
                    $query->whereIn('roles.id', explode(',', $value));
                });
            }),
            AllowedFilter::scope('workStores'),
            AllowedFilter::callback('workStoresId', function($query,$value){
                $query->whereHas('workStores', function($query) use($value){
                    $query->whereIn('stores.id', explode(',', $value));
                });
            }),
            AllowedFilter::callback('salesperson_id', function ($query, $value){
                $query->WhereHas('workStores', function($query) use($value){
                    $query->where('id', $value);
                });
            }),
        ])
        ->allowedIncludes([
            'addresses',
            'preference',
            'notificationPreferences',
            'orders',
            'store',
            'workStores',
            'cartItems',
            'roles',
            'couponUsages'
        ]);

        //  Perform search query
        if(request()->has('q'))
        {
            $q = request()->input('q');

            $users->where(function($query) use($q){
                $query->where('email', 'like',  '%'.$q.'%')
                ->orWhere('first_name', 'like',  '%'.$q.'%')
                ->orWhere('last_name', 'like',  '%'.$q.'%')
                ->orWhere('phone_number', 'like',  '%'.$q.'%');
            });
        }
        
        $users = $users->paginate()
        ->appends(request()->query());

        $user_resource =  UserResource::collection($users);

        $user_resource->with['status'] = "OK";
        $user_resource->with['message'] = 'Users retrived successfully';

        return $user_resource;
    }

    /**
     * Display authenticated user profile
     * 
     * @method GET
     */
    public function profile()
    {
        $user_resource = new UserResource(auth()->user());
        $user_resource->with['message'] = 'Profile retrieved successfully';

        return $user_resource;
    }

    /**
     * Update authenticated user profile
     * 
     * @method POST
     */
    public function updateProfile(UpdateUserRequest $request)
    {
        $validated = $request->validated();

        $user = auth()->user();
        $user->update($validated);

        if(isset($validated['email']))
        {
            $user->is_email_verified = false;
            $user->email_verified_at = null;
        }
        
        $user_resource = new UserResource($user);
        $user_resource->with['message'] = 'Profile updated successfully';

        return $user_resource;
    }

    /**
     * Update authenticated user profile pic
     * 
     * @method PUT || PATCH
     */
    public function updateProfilePhoto(Request $request)
    {
        $user = auth()->user();

        Validator::make($request->all(), [
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
        ])->validateWithBag('updateProfileInformation');

        $user_resource = new UserResource($user);

        if (isset($request->photo)) {
            $user->updateProfilePhoto($request->photo);
            $user->refresh();
            $user_resource->with['message'] = 'Profile photo updated successfully';
        }
        else {
            $user_resource->with['message'] = 'No new photo supplied, profile photo unchange';
        }


        return $user_resource;
    }

    /**
     * Display authenticated user currency preference
     */
    public function indexPreference()
    {
        $currency_preference_resource = new PreferenceResource(auth()->user()->preference);
        $currency_preference_resource->with['message'] = 'Preference retrieved successfully';

        return $currency_preference_resource;
    }

    /**
     * Create or update user's preference 
     */
    public function upsertPreference(StorePreferenceRequest $request)
    {
        $user = auth()->user();
        $validated = $request->validated();

        if(is_null($preference = $user->preference))
        {
            $preference = $user->preference()->create($validated);
        }
        else {

            $preference->updated($validated);
        }

        $preference_resource = new PreferenceResource($preference);
        $preference_resource->with['message'] = 'Preference set successfully';

        return $preference_resource;
    }


     /**
     * Display authenticated user notification preference
     */
    public function indexNotificationPreference()
    {
        $notification_preference_resource = new NotificationPreferenceResource(auth()->user()->notification_preferences);
        $notification_preference_resource->with['message'] = 'Notification preference retrieved successfully';

        return $notification_preference_resource;
    }

    /**
     * subscribe user's to a notification preference 
     */
    public function subscribeNotificationPreference(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'notification_preference_id' => 'uuid|exists:notification_preferences,id',
            'notification_preference_ids' => 'array',
            'notification_preference_ids.*' => 'uuid|exists:notification_preferences,id',

        ]);

        $notify_pre =  array_merge(
                            $validated['notification_preference_ids'] ?? [], 
                            array_key_exists('notification_preference_id',$validated)? [$validated['notification_preference_id']] : []
                        );

        $user->notificationPreferences()->syncWithoutDetaching($notify_pre);
               
        $notification_preference_resource = new NotificationPreferenceResource($user->notificationPreferences);
        $notification_preference_resource->with['message'] = 'Notification preferences subscribed successfully';

        return $notification_preference_resource;
    }


    /**
     * unsubscribe user's to a notification preference 
     */
    public function unsubscribeNotificationPreference(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'notification_preference_id' => 'uuid|exists:notification_preferences,id',
            'notification_preference_ids' => 'array',
            'notification_preference_ids.*' => 'uuid|exists:notification_preferences,id',

        ]);


        $notify_pre =  array_merge(
                            $validated['notification_preference_ids'] ?? [], 
                            array_key_exists('notification_preference_id',$validated)? [$validated['notification_preference_id']] : []
                        );

        $user->notificationPreferences()->detach($notify_pre);

               
        $notification_preference_resource = new NotificationPreferenceResource($user->notificationPreferences);
        $notification_preference_resource->with['message'] = 'Notification preferences unsubscribed successfully';

        return $notification_preference_resource;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        if(request()->has('include'))
        {
            foreach (explode(',', request()->include) as $key => $include) 
            {
               try {
                $user->load($include);
               } catch (\Throwable $th) {
                //throw $th;
               }
            }
        }
        $user_resource = new UserResource($user);
        $user_resource->with['message'] = 'User retrieved successfully';

        return  $user_resource;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }

    /**
     * Display a listing of the resource roles.
     */
    public function roles(User $user)
    {
        $roles = $user->roles();
        $roles_resouce = new RoleResource($roles);
        $roles_resouce->with['message'] = "{$user->first_name}'s roles retrieved successfully";
        return $roles_resouce;
    }

    /**
     * Assign role to user.
     */
    public function assignRole(User $user, Role $role)
    {
        $this->authorize('assign', [$role, $user]);

        $user->roles()->syncWithoutDetaching($role);


        $role_resource = new RoleResource($user->roles);
        $role_resource->with['message'] = "Assigned {$role->name} to {$user->first_name} successfully";

        return $role_resource;
    }

    /**
     * Remove use role.
     *
     * @param App\Models\User $user
     * @param App\Models\Role $role
     */
    public function removeRole(User $user, Role $role)
    {
        $this->authorize('remove', [$role, $user]);

        $user->roles()->detach($role->id);

        $role_resource = new RoleResource($user->roles);
        $role_resource->with['message'] = "Remove {$role->name} from {$user->name} successfully";

        return $role_resource;
    }
}
