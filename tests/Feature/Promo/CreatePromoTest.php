<?php

namespace Tests\Feature\Promo;

use App\Models\User;
Use App\Traits\Testing\WithPermission;
Use App\Traits\Testing\WithPromo;
Use App\Traits\Testing\WithRole;
Use App\Traits\Testing\WithStore;
Use App\Traits\Testing\FastRefreshDatabase;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\Fluent\AssertableJson;

use Tests\TestCase;

class CreatePromoTest extends TestCase
{
    use FastRefreshDatabase;
    use WithFaker;
    use WithPermission;
    use WithPromo;
    use WithRole;
    use WithStore;

    /**
     * User can create a promo test
     *
     * @return void
     */
    public function test_authorized_store_staff_can_create_promo() : void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $user->workStores()->syncWithoutDetaching($this->store);

        $store_keeper = $this->role([
            'name' => 'store-keeper',
            'store_id' => $this->store->id
        ]);

        $user->roles()->syncWithoutDetaching($store_keeper);

        $permission = $this->permission([
            'name' => 'create promo',
        ]);

        $store_keeper->permissions()->syncWithoutDetaching($permission);

        $this->actingAs($user);

        Event::fake();
        $promo = $this->makePromo([
            'store_id' => $this->store->id,
        ]);

        $response = $this->post(route('api.promos.store'), $promo->toArray());

        $response->assertValid();
        $response->assertSuccessful();
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas($promo::class, $promo->only($promo->getFillable()));
        Event::assertDispatched(\App\Events\Promo\PromoCreated::class);
        $response->assertJson(fn (AssertableJson $json) =>$json->etc());
    }

    /**
     * Unauthroized user cannot create promo test
     *
     * @return void
     */
    public function test_unauthorized_user_cannot_create_promo() : void
    {

        $user = User::factory()->create();
        $this->actingAs($user);

        Event::fake();
        $promo = $this->makePromo();

        $response = $this->post(route('api.promos.store'), $promo->toArray());


        $response->assertForbidden();
        $this->assertDatabaseMissing($promo::class, $promo->only($promo->getFillable()));
        Event::assertNotDispatched(\App\Events\Promo\PromoCreated::class);
    }


    /**
     * Store can create a promo test
     *
     * @return void
     */
    public function test_store_owner_can_create_promo() : void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $store = $this->store([
            'user_id'=> $user->id
        ]);

        Event::fake();
        $promo = $this->makePromo([
            'store' => $store->id
        ]);

        $response = $this->post(route('api.promos.store'), $promo->toArray());
        $response->assertValid();
        $response->assertSuccessful();
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas($promo::class, $promo->only($promo->getFillable()));
        Event::assertDispatched(\App\Events\Promo\PromoCreated::class);
        $response->assertJson(fn (AssertableJson $json) =>$json->etc());
    }


    /**
     * Setup promo test environment.
     * 
     * @override Illuminate\Foundation\Testing\TestCase  setUp()
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpPermission();
        $this->setUpPromo();
        $this->setUpRole();
        $this->setUpStore();
    }
}
