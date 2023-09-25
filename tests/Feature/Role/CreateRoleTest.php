<?php

namespace Tests\Feature\Role;

use App\Models\Permission;
Use App\Traits\Testing\FastRefreshDatabase;
Use App\Traits\Testing\WithRole;
Use App\Traits\Testing\WithUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\Fluent\AssertableJson;

use Tests\TestCase;

class CreateRoleTest extends TestCase
{
    use FastRefreshDatabase;
    use WithFaker;
    use WithRole;
    use WithUser;

     /**
     * Super admin can create a role test
     *
     * @return void
     */
    public function test_super_admin_can_create_role() : void
    {        
        $super_admin_role = $this->role([
            'name' => 'super-admin',
            'store_id' => null
        ]);

        $this->user->roles()->syncWithoutDetaching($super_admin_role);
        $this->actingAs($this->user);

        Event::fake();
        $role = $this->makeRole();
        $response = $this->post(route('api.roles.store'), $role->toArray());

        $response->assertValid();
        $response->assertSuccessful();
        $response->assertSessionHasNoErrors();
        $response->assertCreated();
        $this->assertDatabaseHas($role::class, $role->only($role->getFillable()));
        Event::assertDispatched(\App\Events\Role\RoleCreated::class);
        $response->assertJson(fn (AssertableJson $json) =>$json->etc());
    }

     /**
     * Authorized user role can create role test
     *
     * @return void
     */
    public function test_authorized_user_role_can_create_role() : void
    {
        $store_admin_role = $this->role([
            'name' => 'store-admin',
            'store_id' => null
        ]);

        $create_role_perm = Permission::firstOrCreate(['name' => 'create role']);
        $store_admin_role->permissions()->syncWithoutDetaching($create_role_perm);

        $this->user->roles()->syncWithoutDetaching($store_admin_role);
        $this->actingAs($this->user);
               
        Event::fake();
        $role = $this->makeRole();
        $response = $this->post(route('api.roles.store'), $role->toArray());
        $response->assertValid();
        $response->assertSuccessful();
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas($role::class, $role->only($role->getFillable()));
        Event::assertDispatched(\App\Events\Role\RoleCreated::class);
        $response->assertJson(fn (AssertableJson $json) =>$json->etc());
    }

    /**
     * Unauthroized user role cannot create role test
     *
     * @return void
     */
    public function test_unauthorized_user_role_cannot_create_role() : void
    {
        $any_role = $this->role([
            'name' => 'any role',
            'store_id' => null
        ]);

        $this->user->roles()->syncWithoutDetaching($any_role);

        $this->actingAs($this->user);

        Event::fake();
        $role = $this->makeRole();
        $response = $this->post(route('api.roles.store'), $role->toArray());

        $response->assertForbidden();
        $this->assertDatabaseMissing($role::class, $role->only($role->getFillable()));
        Event::assertNotDispatched(\App\Events\Role\RoleCreated::class);
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
        $this->setUpUser();
    }
}
