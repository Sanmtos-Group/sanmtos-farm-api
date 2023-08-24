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

class DeleteStoreTest extends TestCase
{
    use FastRefreshDatabase;
    use WithFaker;
    use WithStore;

    /**
     * User can update a store test
     *
     * @return void
     */
    public function test_user_can_delete_store() : void
    {
        $user = $this->store->user;
        $this->actingAs($user);

        $response = $this->delete(route('api.stores.destroy', $this->store));
        $this->store->refresh();
        $response->assertValid();
        $response->assertOk();
        $response->assertSessionHasNoErrors();
        $this->assertSoftDeleted($this->store);
        $this->assertDatabaseHas($this->store::class, $this->store->only($this->store->getFillable()));
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
