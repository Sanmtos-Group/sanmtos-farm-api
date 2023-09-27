<?php

namespace Tests\Feature\Role;

use App\Models\Permission;
Use App\Traits\Testing\FastRefreshDatabase;
Use App\Traits\Testing\WithPermission;
Use App\Traits\Testing\WithRole;
Use App\Traits\Testing\WithStore;
Use App\Traits\Testing\WithUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\Fluent\AssertableJson;

use Tests\TestCase;

class UserRoleTest extends TestCase
{
    use FastRefreshDatabase;
    use WithFaker;
    use WithPermission;
    use WithRole;
    use WithStore;
    use WithUser;

     /**
     * Super admin can grant a user role test
     *
     * @return void
     */
    public function test_super_admin_can_assign_user_role() : void
    {        
        $super_admin_role = $this->role([
            'name' => 'super-admin',
            'store_id' => null
        ]);

        $this->user->roles()->syncWithoutDetaching($super_admin_role);
        $this->actingAs($this->user);

        Event::fake();

        $user = $this->user([]);
        $response = $this->put(route('api.users.roles.assign',['user'=> $user, 'role'=>$this->role]));

        $response->assertValid();
        $response->assertSuccessful();
        $response->assertSessionHasNoErrors();
        $this->assertInstanceOf(\App\Models\Role::class, $user->roles()->find($this->role->id));
        $response->assertJson(fn (AssertableJson $json) =>$json->etc());
    }

     /**
     * Super admin can revole a role permission test
     *
     * @return void
     */
    public function test_super_admin_can_remove_user_role() : void
    {        
        $super_admin_role = $this->role([
            'name' => 'super-admin',
            'store_id' => null
        ]);

        $this->user->roles()->syncWithoutDetaching($super_admin_role);
        $this->actingAs($this->user);

        Event::fake();

        $user = $this->user([]);


        $user->roles()->syncWithoutDetaching($this->role);
        $response = $this->delete(route('api.users.roles.remove',['user'=> $user, 'role'=>$this->role]));

        $response->assertValid();
        $response->assertSuccessful();
        $response->assertSessionHasNoErrors();
        $this->assertNull($this->user->roles()->find($this->role->id));
        $response->assertJson(fn (AssertableJson $json) =>$json->etc());
    }

    /**
     * authorized staff can assign  role test
     *
     * @return void
     */
    public function test_authorized_staff_can_assign_role() : void
    {   
        $staff_role = $this->role([
            'name' => 'staff',
            'store_id' => null
        ]);

        $this->user->is_staff = true;
        $this->user->save();
        $this->user->roles()->syncWithoutDetaching($staff_role);
        $this->actingAs($this->user);

        $assign_role_permission = $this->permission(['name'=>'assign role']);
        $staff_role->permissions()->syncWithoutDetaching($assign_role_permission);

        $new_user = $this->user([]);
        $new_user->is_staff = true;
        $new_user->save();

        $new_role = $this->role([
            'name' => 'new-role',
            'store_id' => null
        ]);

        Event::fake();

        $response = $this->put(route('api.users.roles.assign',['user'=> $new_user, 'role'=>$new_role]));

        $response->assertValid();
        $response->assertSuccessful();
        $response->assertSessionHasNoErrors();
        $this->assertInstanceOf(\App\Models\Role::class, $new_user->roles()->find($new_role->id));
        $response->assertJson(fn (AssertableJson $json) =>$json->etc());
    }

    /**
     * unauthorized staff cannot assign  role test
     *
     * @return void
     */
    public function test_unauthorized_staff_cannot_assign_role() : void
    {   
        $staff_role = $this->role([
            'name' => 'staff',
            'store_id' => null
        ]);

        $this->user->is_staff = true;
        $this->user->save();
        $this->user->roles()->syncWithoutDetaching($staff_role);
        $this->actingAs($this->user);


        $new_user = $this->user([]);
        $new_user->is_staff = true;
        $new_user->save();

        $new_role = $this->role([
            'name' => 'new-role',
            'store_id' => null
        ]);

        Event::fake();

        $response = $this->put(route('api.users.roles.assign',['user'=> $new_user, 'role'=>$new_role]));

        $response->assertValid();
        $response->assertForbidden();
        $this->assertNull($new_user->roles()->find($new_role->id));
    }

     /**
     * Uauthorized store owner can assign role test
     *
     * @return void
     */
    public function test_authorized_store_owner_can_assign_role() : void
    {   
        $store_owner = $this->store->owner;
        $store_admin_role = $this->role([
            'name' => 'store-admin',
            'store_id' => null
        ]);

        $store_owner->roles()->syncWithoutDetaching($store_admin_role);
        $assign_role_permission = $this->permission(['name'=>'assign role']);
        $store_admin_role->permissions()->syncWithoutDetaching($assign_role_permission);
        $this->user->workStores()->syncWithoutDetaching($this->store);
        $this->actingAs($store_owner);

        $new_store_role = $this->role([
            'name' => 'new-store-role',
            'store_id' => $this->store->id
        ]);

        Event::fake();

        $response = $this->put(route('api.users.roles.assign',['user'=> $this->user, 'role'=>$new_store_role]));

        $response->assertValid();
        $response->assertSuccessful();
        $response->assertSessionHasNoErrors();
        $this->assertInstanceOf(\App\Models\Role::class, $this->user->roles()->find($new_store_role->id));
        $response->assertJson(fn (AssertableJson $json) =>$json->etc());
    }


    /**
     * Unauthorized store owner cannot assign role test
     *
     * @return void
     */
    public function test_unauthorized_store_owner_cannot_assign_role() : void
    {   
        $store_owner = $this->store->owner;
        $store_admin_role = $this->role([
            'name' => 'store-admin',
            'store_id' => null
        ]);

        $store_owner->roles()->syncWithoutDetaching($store_admin_role);

        $this->user->workStores()->syncWithoutDetaching($this->store);
        $this->actingAs($store_owner);

        $new_store_role = $this->role([
            'name' => 'new-store-role',
            'store_id' => $this->store->id
        ]);

        Event::fake();

        $response = $this->put(route('api.users.roles.assign',['user'=> $this->user, 'role'=>$new_store_role]));

        $response->assertValid();
        $response->assertForbidden();
        $this->assertNull($this->user->roles()->find($new_store_role->id));
    }

     /**
     * authorized store staff can assign  role test
     *
     * @return void
     */
    public function test_authorized_store_staff_can_assign_role() : void
    {   
        $store_staff_role = $this->role([
            'name' => 'store-staff',
            'store_id' => $this->store->id
        ]);
        $this->user->workStores()->syncWithoutDetaching($this->store);
        $this->user->roles()->syncWithoutDetaching($store_staff_role);

        $assign_role_permission = $this->permission(['name'=>'assign role']);
        $store_staff_role->permissions()->syncWithoutDetaching($assign_role_permission);

        $new_staff = $this->user([]);
        $new_staff->workStores()->syncWithoutDetaching($this->store);
        $this->actingAs($this->user);

        $new_store_role = $this->role([
            'name' => 'new-store-role',
            'store_id' => $this->store->id
        ]);

        Event::fake();

        $response = $this->put(route('api.users.roles.assign',['user'=> $new_staff, 'role'=>$new_store_role]));

        $response->assertValid();
        $response->assertSuccessful();
        $response->assertSessionHasNoErrors();
        $this->assertInstanceOf(\App\Models\Role::class, $new_staff->roles()->find($new_store_role->id));
        $response->assertJson(fn (AssertableJson $json) =>$json->etc());
    }


    /**
     * unathorized store staff cannot assign role test
     *
     * @return void
     */
    public function test_unauthorized_store_staff_cannot_assign_role() : void
    {   
        $store_staff_role = $this->role([
            'name' => 'store-staff',
            'store_id' => $this->store->id
        ]);
        $this->user->workStores()->syncWithoutDetaching($this->store);
        $this->user->roles()->syncWithoutDetaching($store_staff_role);

        $new_staff = $this->user([]);
        $new_staff->workStores()->syncWithoutDetaching($this->store);
        $this->actingAs($this->user);

        $new_store_role = $this->role([
            'name' => 'new-store-role',
            'store_id' => $this->store->id
        ]);

        Event::fake();

        $response = $this->put(route('api.users.roles.assign',['user'=> $new_staff, 'role'=>$new_store_role]));

        $response->assertValid();
        $response->assertForbidden();
        $this->assertNull($this->user->roles()->find($new_store_role->id));
    }


    /**
     * Setup role test environment.
     * 
     * @override Illuminate\Foundation\Testing\TestCase  setUp()
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpPermission();
        $this->setUpRole();
        $this->setUpStore();
        $this->setUpUser();
    }
}
