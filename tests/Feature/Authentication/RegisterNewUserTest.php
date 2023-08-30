<?php

namespace Tests\Feature\Authentication;

Use App\Traits\Testing\WithUser;
Use App\Traits\Testing\FastRefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class RegisterNewUserTest extends TestCase
{
    use FastRefreshDatabase;
    use WithUser;
    
    /**
     * New user can register test.
     */
    public function test_new_user_can_register(): void
    {
        $user = $this->makeUser([
            'email_verified_at' => null,
        ]);

        $new_user_data = array_merge($user->toArray(), [ 'password' => 'password', 'password_confirmation'=>'password']);
        $response = $this->post(route('api.register'), $new_user_data);

        $response->assertValid();
        $response->assertSuccessful();
        $response->assertSessionHasNoErrors();
        $user_fillable = $user->getFillable();

        if($password_index = array_search('password', $user_fillable)){
           unset($user_fillable[$password_index]);
        }

        $this->assertDatabaseHas($user::class, $user->only($user_fillable));
        $response->assertJson(fn (AssertableJson $json) =>$json->etc());
    }

    /**
     * Setup user test environment.
     * 
     * @override Illuminate\Foundation\Testing\TestCase  setUp()
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpUser();
    }
}
