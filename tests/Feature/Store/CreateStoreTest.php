<?php

namespace Tests\Feature\Store;

use App\Models\User;
Use App\Traits\Testing\WithStore;
Use App\Traits\Testing\FastRefreshDatabase;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\Fluent\AssertableJson;

use Tests\TestCase;

class CreateStoreTest extends TestCase
{
    use FastRefreshDatabase;
    use WithFaker;
    use WithStore;

    /**
     * User can create a store test
     *
     * @return void
     */
    public function test_user_can_create_store() : void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Event::fake();
        $store = $this->makeStore(['user_id', $user->id]);
        $response = $this->post(route('api.stores.store'), $store->toArray());

        $response->assertValid();
        $response->assertSuccessful();
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas($store::class, $store->only($store->getFillable()));
        Event::assertDispatched(\App\Events\Store\StoreCreated::class);
        $response->assertJson(fn (AssertableJson $json) =>$json->etc());
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
    }
}
