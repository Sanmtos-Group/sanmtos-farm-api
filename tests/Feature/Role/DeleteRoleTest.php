<?php

namespace Tests\Feature\Role;

use App\Models\Permission;
Use App\Traits\Testing\WithRole;
Use App\Traits\Testing\WithStore;
Use App\Traits\Testing\WithUser;
Use App\Traits\Testing\FastRefreshDatabase;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\Fluent\AssertableJson;

use Tests\TestCase;

class DeleteRoleTest extends TestCase
{
    use FastRefreshDatabase;
    use WithFaker;
    use WithRole;
    use WithStore;
    use WithUser;

    /**
     * Super admin can delete role test
     *
     * @return void
     */
    public function test_super_admin_can_delete_role() : void
    {
        $super_admin_role = $this->role([
            'name' => 'super-admin',
            'store_id' => null
        ]);

        $this->user->roles()-> syncWithoutDetaching($super_admin_role);
        
        $this->actingAs($this->user);

        Event::fake();
        $response = $this->delete(route('api.roles.destroy', $this->role));

        $response->assertValid();
        $response->assertOk();
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseMissing($this->role::class, $this->role->only($this->role->getFillable()));
        Event::assertDispatched(\App\Events\Role\RoleDeleted::class);
        $response->assertJson(fn (AssertableJson $json) =>$json->etc());
    }

    /**
     * Authroized staff can delete non-store role test
     *
     * @return void
     */
    public function test_authorized_staff_can_delete_non_store_role() : void
    {
        $non_store_role = $this->role([
            'name' => 'staff',
            'store_id' => null
        ]);

        $delete_role_perm = Permission::firstOrCreate(['name' => 'delete role']);
        $non_store_role->permissions()->syncWithoutDetaching($delete_role_perm);
        
        $this->user->is_staff = true;
        $this->user->save();
        $this->user->roles()->syncWithoutDetaching($non_store_role);
        $this->actingAs($this->user);

        Event::fake();

        $response = $this->delete(route('api.roles.destroy', $non_store_role));

        $response->assertValid();
        $response->assertOk();
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseMissing($non_store_role::class, $non_store_role->only($non_store_role->getFillable()));
        Event::assertDispatched(\App\Events\Role\RoleDeleted::class);
        $response->assertJson(fn (AssertableJson $json) =>$json->etc());
    }

    /**
     * Authorized store staff can delete store role test
     *
     * @return void
     */
    public function test_authorized_store_staff_can_delete_store_role() : void
    {
        $store_role = $this->role([
            'name' => 'store staff',
            'store_id' => $this->store->id
        ]);

        $delete_role_perm = Permission::firstOrCreate(['name' => 'delete role']);
        $store_role->permissions()->syncWithoutDetaching($delete_role_perm);

        $this->user->workStores()->syncWithoutDetaching($this->store);
        $this->user->roles()->syncWithoutDetaching($store_role);
        $this->actingAs($this->user);

        Event::fake();
        $response = $this->delete(route('api.roles.destroy', $store_role));

        $response->assertValid();
        $response->assertOk();
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseMissing($store_role::class, $store_role->only($store_role->getFillable()));
        Event::assertDispatched(\App\Events\Role\RoleDeleted::class);
        $response->assertJson(fn (AssertableJson $json) =>$json->etc());
    }

    /**
     * Authorized store staff cannot delete other store role test
     *
     * @return void
     */
    public function test_authorized_store_staff_cannot_delete_other_store_role() : void
    {
        $store_role = $this->role([
            'name' => 'store staff',
            'store_id' => $this->store->id
        ]);

        $delete_role_perm = Permission::firstOrCreate(['name' => 'delete role']);
        $store_role->permissions()->syncWithoutDetaching($delete_role_perm);

        $this->user->workStores()->syncWithoutDetaching($this->store);
        $this->user->roles()->syncWithoutDetaching($store_role);
        $this->actingAs($this->user);

        Event::fake();

        $other_store_role = $this->role([
            'name' => 'other store staff',
            'store_id' => $this->store([])->id
        ]);

        $response = $this->delete(route('api.roles.destroy', $other_store_role));

        $response->assertValid();
        $response->assertForbidden();
        $this->assertDatabaseHas($other_store_role::class, $other_store_role->only($other_store_role->getFillable()));
        Event::assertNotDispatched(\App\Events\Role\RoleDeleted::class);
    }

    /**
     * Unauthroized store staff can delete store role test
     *
     * @return void
     */
    public function test_unauthorized_store_staff_cannot_delete_store_role() : void
    {
        $store_role = $this->role([
            'name' => 'store staff',
            'store_id' => $this->store->id
        ]);

        $delete_role_perm = Permission::firstOrCreate(['name' => 'delete role']);
        // $store_role->permissions()->syncWithoutDetaching($delete_role_perm);

        $this->user->workStores()->syncWithoutDetaching($this->store);
        $this->user->roles()->syncWithoutDetaching($store_role);
        $this->actingAs($this->user);

        Event::fake();
        $response = $this->delete(route('api.roles.destroy', $store_role));

        $response->assertValid();
        $response->assertForbidden();
        $this->assertDatabaseHas($store_role::class, $store_role->only($store_role->getFillable()));
        Event::assertNotDispatched(\App\Events\Role\RoleDeleted::class);
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
