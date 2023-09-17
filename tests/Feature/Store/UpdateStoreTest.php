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

class UpdateStoreTest extends TestCase
{
    use FastRefreshDatabase;
    use WithFaker;
    use WithStore;

    /**
     * User can update a store test
     *
     * @return void
     */
    public function test_user_can_update_store() : void
    {
        $user = $this->store->user;
        $this->actingAs($user);

        $this->store->name = $this->faker()->unique()->name();

        Event::fake();
        $response = $this->patch(route('api.stores.update', $this->store), $this->store->toArray());
        $this->store->refresh();

        $response->assertValid();
        $response->assertOk();
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas($this->store::class, $this->store->only($this->store->getFillable()));
        Event::assertDispatched(\App\Events\Store\StoreUpdated::class);
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
