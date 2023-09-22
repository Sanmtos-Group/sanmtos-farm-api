<?php

namespace Tests\Feature\Store;

Use App\Traits\Testing\WithStore;
Use App\Traits\Testing\WithUser;
Use App\Traits\Testing\FastRefreshDatabase;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\Fluent\AssertableJson;

use Tests\TestCase;

class UserRelationshipTest extends TestCase
{
    use FastRefreshDatabase;
    use WithFaker;
    use WithStore;
    use WithUser;

    /**
     * Store has an owner products test
     *
     * @return void
     */
    public function test_user_can_own_a_store() : void
    {
        $this->actingAs($this->user);


        Event::fake();
        $this->assertDatabaseHas($this->user::class, $this->user->only($this->user->getFillable()));

        if(empty($this->user->store)){
            $this->assertIsBool(empty($this->user->store));
            $this->assertEquals(0, $this->user->store()->count());
            $user_store = $this->store(['user_id'=>$this->user->id, 'slug'=>'user-store']);

        }else {
            $user_store = $this->store(['user_id'=>$this->user->id,'slug'=>'user-store']);
        }
        
        $this->assertEquals(1, $this->user->store()->count());
        $this->assertInstanceOf(\App\Models\Store::class, $user_store);
        $this->assertDatabaseHas($user_store::class, $user_store->only($user_store->getFillable()));

    }

    /**
     * Setup store test environment.
     * 
     * @override Illuminate\Foundation\Testing\TestCase  setUp()
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpStore();
        $this->setUpUser();

    }
}
