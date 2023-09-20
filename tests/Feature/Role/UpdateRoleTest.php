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

class UpdateRoleTest extends TestCase
{
    use FastRefreshDatabase;
    use WithFaker;
    use WithRole;

    /**
     * User can update a role test
     *
     * @return void
     */
    public function test_user_can_update_role() : void
    {
        $user = User::first()?? User::factory()->create();
        $this->actingAs($user);

        $this->role->name = $this->faker()->unique()->name();

        Event::fake();
        $response = $this->patch(route('api.roles.update', $this->role), $this->role->toArray());
        $this->role->refresh();

        $response->assertValid();
        $response->assertOk();
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas($this->role::class, $this->role->only($this->role->getFillable()));
        Event::assertDispatched(\App\Events\Role\RoleUpdated::class);
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
