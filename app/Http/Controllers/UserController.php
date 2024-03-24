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
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        $user_resource = new UserResource($users);
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
     * Display authenticated user addresses
     */
    public function indexAddress()
    {
        $adresses_resource = new AddressResource(auth()->user()->addresses);
        $adresses_resource->with['message'] = 'Addresses retrieved successfully';

        return $adresses_resource;
    }

    /**
     * Create new address for authenticated user
     */
    public function storeAddress(StoreAddressRequest $request)
    {
        $user = auth()->user();
        $validated = $request->validated();

        $validated['first_name'] = array_key_exists('first_name', $validated) ? $validated['first_name'] : $user->first_name;
        $validated['last_name'] = array_key_exists('last_name', $validated) ? $validated['last_name'] : $user->last_name;
        $validated['dialing_code'] = array_key_exists('dialing_code', $validated) ? $validated['dialing_code'] : $user->dialing_code;
        $validated['phone_number'] = array_key_exists('phone_number', $validated) ? $validated['phone_number'] : $user->phone_number;

        $user->addresses()->create($validated);

        $user = $request->user();
        $adresses_resource = new AddressResource($user->addresses);
        $adresses_resource->with['message'] = 'Addresses retrieved successfully';

        return $adresses_resource;
    }

    /**
     * Update new address for authenticated user
     */
    public function updateAddress(UpdateAddressRequest $request, Address $address)
    {
        // only the owner of the address can edit the address
        if(is_null(auth()->user()->addresses('id', $address->id)->first()))
        {
            return response()->json([
                'message' => "This action is unauthorized.",
            ], 403);
        }

        $validated = $request->validated();
        $validated['first_name'] = array_key_exists('first_name', $validated) ? $validated['first_name'] : $address->first_name;
        $validated['last_name'] = array_key_exists('last_name', $validated) ? $validated['last_name'] : $address->last_name;
        $validated['dialing_code'] = array_key_exists('dialing_code', $validated) ? $validated['dialing_code'] : $address->dialing_code;
        $validated['phone_number'] = array_key_exists('phone_number', $validated) ? $validated['phone_number'] : $address->phone_number;

        $address->update($validated);

        $user = $request->user();
        $adresses_resource = new AddressResource($address);
        $adresses_resource->with['message'] = 'Address updated successfully';

        return $adresses_resource;
    }

    /**
     * Display a specified user's address
     */
    public function showAddress(Address $address)
    {
        // only the owner of the address can see the address
        if(is_null(auth()->user()->addresses('id', $address->id)->first()))
        {
            return response()->json([
                'message' => "This action is unauthorized.",
            ], 403);
        }

        $adresses_resource = new AddressResource($address);
        $adresses_resource->with['message'] = 'Address retrived successfully';

        return $adresses_resource;
    }

    /**
     * Update new address for authenticated user
     */
    public function deleteAddress(Address $address)
    {
        // only the owner of the address can edit the address
        if(is_null(auth()->user()->addresses('id', $address->id)->first()))
        {
            return response()->json([
                'message' => "This action is unauthorized.",
            ], 403);
        }

        $address->delete();

        $adresses_resource = new AddressResource(null);
        $adresses_resource->with['message'] = 'Address deleted successfully';

        return $adresses_resource;
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
     * Display a listing users that are staff.
     */
    public function staffs()
    {
        $users = User::where('is_staff', true)->get();
        $user_resource = new UserResource($users);

        $user_resource->with['message'] = 'Sanmtos staffs retrieved successfully';

        return $user_resource;
    }


    /**
     * Display a listing users that are store staff.
     *
     */
    public function storeStaffs()
    {
        $users = User::has('workStores')->get();

        $user_resource = new UserResource($users);

        $user_resource->with['message'] = 'Stores staffs retrieved successfully';

        return $user_resource;

    }

    /**
     * Display authenticated user orders
     */
    public function indexOrders()
    {
        $user = auth()->user();

        $orders = QueryBuilder::for(\App\Models\Order::class)
        ->defaultSort('-created_at')
        ->allowedSorts(
            'price',
            'total_price',
            'status',
            AllowedSort::custom('recent', new \App\Models\Sorts\LatestSort()),
            AllowedSort::custom('oldest', new \App\Models\Sorts\OldestSort()),
        )
        ->allowedFilters([
            AllowedFilter::exact('user_id')->default($user->id),
            AllowedFilter::exact('status'),
            AllowedFilter::exact('is_paid'),
            AllowedFilter::exact('ordered_at')->ignore(null),
        ])
        ->allowedIncludes([
            'payment',
            'orderables',
        ])
        ->paginate()
        ->appends(request()->query());

        $order_resource =  OrderResource::collection($orders);

        $order_resource->with['status'] = "OK";
        $order_resource->with['message'] = 'Orders retrived successfully';

        return $order_resource;
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
        //
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
