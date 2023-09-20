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

class DeleteRoleTest extends TestCase
{
    use FastRefreshDatabase;
    use WithFaker;
    use WithRole;

    /**
     * User can delete a role test
     *
     * @return void
     */
    public function test_user_can_delete_role() : void
    {
        $user = User::first()?? User::factory()->create();
        $this->actingAs($user);

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
