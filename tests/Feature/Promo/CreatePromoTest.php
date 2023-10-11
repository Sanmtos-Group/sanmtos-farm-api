<?php

namespace Tests\Feature\Promo;

use App\Models\User;
Use App\Traits\Testing\WithPromo;
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
    use WithPromo;

    /**
     * User can create a promo test
     *
     * @return void
     */
    public function test_user_can_create_promo() : void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Event::fake();
        $promo = $this->makePromo();

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
        $this->setUpPromo();
    }
}
