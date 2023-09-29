<?php

namespace Tests\Feature\Role;

use App\Models\Permission;
Use App\Traits\Testing\FastRefreshDatabase;
Use App\Traits\Testing\WithRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\Fluent\AssertableJson;

use Tests\TestCase;

class ReadRoleTest extends TestCase
{
    use FastRefreshDatabase;
    use WithFaker;
    use WithRole;

     /**
     * User can read  role test
     *
     * @return void
     */
    public function test_authenticated_user_can_read_all_roles() : void
    {   

        Event::fake();
        $response = $this->get(route('api.roles.index'));
        
        $response->assertValid();
        $response->assertSuccessful();
        $response->assertSessionHasNoErrors();
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
