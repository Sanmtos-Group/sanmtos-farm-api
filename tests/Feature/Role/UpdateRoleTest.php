<?php

namespace Tests\Feature\Role;

use App\Models\Permission;
Use App\Traits\Testing\FastRefreshDatabase;
Use App\Traits\Testing\WithRole;
Use App\Traits\Testing\WithStore;
Use App\Traits\Testing\WithUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\Fluent\AssertableJson;

use Tests\TestCase;

class UpdateRoleTest extends TestCase
{
    use FastRefreshDatabase;
    use WithFaker;
    use WithRole;
    use WithStore;
    use WithUser;

    /**
     * Super admin can update role test
     *
     * @return void
     */
    public function test_super_admin_can_update_role() : void
    {
        $super_admin_role = $this->role([
            'name' => 'super-admin',
            'store_id' => null
        ]);

        $this->user->roles()-> syncWithoutDetaching($super_admin_role);
        
        $this->actingAs($this->user);

        Event::fake();
        $new_role_name = 'new role name';
        $response = $this->patch(route('api.roles.update', $this->role), ['name'=> $new_role_name]);
        $this->role->refresh();

        $response->assertValid();
        $response->assertOk();
        $response->assertSessionHasNoErrors();
        $this->assertEquals($this->role->name, $new_role_name );
        $this->assertDatabaseHas($this->role::class, $this->role->only($this->role->getFillable()));
        Event::assertDispatched(\App\Events\Role\RoleUpdated::class);
        $response->assertJson(fn (AssertableJson $json) =>$json->etc());
    }

    /**
     * Authorized staff can update non-store role test
     *
     * @return void
     */
    public function test_authorized_staff_can_update_non_store_role() : void
    {
        $non_store_role = $this->role([
            'name' => 'staff',
            'store_id' => null
        ]);

        $update_role_perm = Permission::firstOrCreate(['name' => 'update role']);
        $non_store_role->permissions()->syncWithoutDetaching($update_role_perm);
        
        $this->user->is_staff = true;
        $this->user->save();
        $this->user->roles()->syncWithoutDetaching($non_store_role);
        $this->actingAs($this->user);

        Event::fake();
        $new_role_name = 'new store role name';
        $response = $this->patch(route('api.roles.update', $non_store_role), ['name'=> $new_role_name]);
        $non_store_role->refresh();

        $response->assertValid();
        $response->assertOk();
        $response->assertSessionHasNoErrors();
        $this->assertEquals($non_store_role->name, $new_role_name );
        $this->assertDatabaseHas($non_store_role::class, $non_store_role->only($non_store_role->getFillable()));
        Event::assertDispatched(\App\Events\Role\RoleUpdated::class);
        $response->assertJson(fn (AssertableJson $json) =>$json->etc());
    }

    /**
     * Authorized store staff can update store role test
     *
     * @return void
     */
    public function test_authorized_store_staff_can_update_store_role() : void
    {
        $store_role = $this->role([
            'name' => 'store staff',
            'store_id' => $this->store->id
        ]);

        $update_role_perm = Permission::firstOrCreate(['name' => 'update role']);
        $store_role->permissions()->syncWithoutDetaching($update_role_perm);

        $this->user->workStores()->syncWithoutDetaching($this->store);
        $this->user->roles()->syncWithoutDetaching($store_role);
        $this->actingAs($this->user);

        Event::fake();
        $new_role_name = 'new store role name';
        $response = $this->patch(route('api.roles.update', $store_role), ['name'=> $new_role_name]);
        $store_role->refresh();

        $response->assertValid();
        $response->assertOk();
        $response->assertSessionHasNoErrors();
        $this->assertEquals($store_role->name, $new_role_name );
        $this->assertDatabaseHas($store_role::class, $store_role->only($store_role->getFillable()));
        Event::assertDispatched(\App\Events\Role\RoleUpdated::class);
        $response->assertJson(fn (AssertableJson $json) =>$json->etc());
    }

    /**
     * Authorized store staff cannot update other store role test
     *
     * @return void
     */
    public function test_authorized_store_staff_cannot_update_other_store_role() : void
    {
        $store_role = $this->role([
            'name' => 'store staff',
            'store_id' => $this->store->id
        ]);

        $update_role_perm = Permission::firstOrCreate(['name' => 'update role']);
        $store_role->permissions()->syncWithoutDetaching($update_role_perm);

        $this->user->workStores()->syncWithoutDetaching($this->store);
        $this->user->roles()->syncWithoutDetaching($store_role);
        $this->actingAs($this->user);

        Event::fake();
        $new_role_name = 'new store role name';
        $other_store_role = $this->role([
            'name' => 'other store staff',
            'store_id' => $this->store([])->id
        ]);

        $response = $this->patch(route('api.roles.update', $other_store_role), ['name'=> $new_role_name]);
        $other_store_role->refresh();

        $response->assertForbidden();
        $this->assertNotEquals($other_store_role->name, $new_role_name );
        Event::assertNotDispatched(\App\Events\Role\RoleUpdated::class);
    }

    /**
     * Unauthroized store staff can update store role test
     *
     * @return void
     */
    public function test_unauthorized_store_staff_cannot_update_store_role() : void
    {
        $store_role = $this->role([
            'name' => 'store staff',
            'store_id' => $this->store->id
        ]);

        $update_role_perm = Permission::firstOrCreate(['name' => 'update role']);
        // $store_role->permissions()->syncWithoutDetaching($update_role_perm);

        $this->user->workStores()->syncWithoutDetaching($this->store);
        $this->user->roles()->syncWithoutDetaching($store_role);
        $this->actingAs($this->user);

        Event::fake();
        $new_role_name = 'new store role name';
        $response = $this->patch(route('api.roles.update', $store_role), ['name'=> $new_role_name]);
        $store_role->refresh();

        $response->assertForbidden();
        $this->assertNotEquals($store_role->name, $new_role_name );
        Event::assertNotDispatched(\App\Events\Role\RoleUpdated::class);
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
        $this->setUpRole();
        $this->setUpStore();
        $this->setUpUser();
    }
}
