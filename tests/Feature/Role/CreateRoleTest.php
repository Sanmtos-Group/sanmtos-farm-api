<?php

namespace Tests\Feature\Role;

use App\Models\User;
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

    /**
     * User can create a role test
     *
     * @return void
     */
    public function test_user_can_create_role() : void
    {
        $user = User::factory()->create();
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
