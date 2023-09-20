<?php

namespace Tests\Feature\Role;

use App\Models\User;
use App\Models\Role;
Use App\Traits\Testing\WithRole;
Use App\Traits\Testing\FastRefreshDatabase;
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

        // 'super-admin',
        // 'admin',
        // 'sanmtos-salesperson',
        // 'store-admin',

     /**
     * Super admin can create a role test
     *
     * @return void
     */
    public function test_super_admin_can_create_role() : void
    {
        $user = User::factory()->create();
        $super_admin_role = Role::firstORCreate([
            'name' => 'super-admin',
            'store_id' => null
        ]);

        if(!$user->hasRole($super_admin_role)){
            $user->roles()->attach($super_admin_role->id);
        }

        $this->actingAs($user);

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
     * Admin can create a role test
     *
     * @return void
     */
    public function test_admin_can_create_role() : void
    {
        $user = User::factory()->create();
        $admin_role = Role::firstORCreate([
            'name' => 'admin',
            'store_id' => null
        ]);

        if(!$user->hasRole($admin_role)){
            $user->roles()->attach($admin_role->id);
        }

        $this->actingAs($user);

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
     * Store admin can create a role test
     *
     * @return void
     */
    public function test_store_admin_can_create_role() : void
    {
        $user = User::factory()->create();
        $store_admin_role = Role::firstORCreate([
            'name' => 'store-admin',
            'store_id' => null
        ]);

        if(!$user->hasRole($store_admin_role)){
            $user->roles()->attach($store_admin_role->id);
        }

        $this->actingAs($user);

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
     * None authorized cannot create a role test
     *
     * @return void
     */
    public function test_non_authorized_user_cannot_create_role() : void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

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
    }
}
