<?php

namespace Tests\Feature\Role;

use App\Models\Permission;
Use App\Traits\Testing\FastRefreshDatabase;
Use App\Traits\Testing\WithPermission;
Use App\Traits\Testing\WithRole;
Use App\Traits\Testing\WithUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\Fluent\AssertableJson;

use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use FastRefreshDatabase;
    use WithFaker;
    use WithPermission;
    use WithRole;
    use WithUser;

     /**
     * Super admin can grant a role permission test
     *
     * @return void
     */
    public function test_super_admin_can_grant_role_permission() : void
    {        
        $super_admin_role = $this->role([
            'name' => 'super-admin',
            'store_id' => null
        ]);

        $this->user->roles()->syncWithoutDetaching($super_admin_role);
        $this->actingAs($this->user);

        Event::fake();

        $response = $this->put(route('api.roles.permissions.grant',['role'=> $this->role, 'permission'=>$this->permission]));

        $response->assertValid();
        $response->assertSuccessful();
        $response->assertSessionHasNoErrors();
        $this->assertInstanceOf(\App\Models\Permission::class, $this->role->permissions()->find($this->permission->id));
        $response->assertJson(fn (AssertableJson $json) =>$json->etc());
    }

     /**
     * Super admin can revole a role permission test
     *
     * @return void
     */
    public function test_super_admin_can_revoke_role_permission() : void
    {        
        $super_admin_role = $this->role([
            'name' => 'super-admin',
            'store_id' => null
        ]);

        $this->user->roles()->syncWithoutDetaching($super_admin_role);
        $this->actingAs($this->user);

        Event::fake();

        $this->role->permissions()->syncWithoutDetaching($this->permission);
        $response = $this->delete(route('api.roles.permissions.revoke',['role'=> $this->role, 'permission'=>$this->permission]));

        $response->assertValid();
        $response->assertSuccessful();
        $response->assertSessionHasNoErrors();
        $this->assertNull($this->role->permissions()->find($this->permission->id));
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
        $this->setUpPermission();
        $this->setUpRole();
        $this->setUpUser();
    }
}
