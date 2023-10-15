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

class UpdatePromoTest extends TestCase
{
    use FastRefreshDatabase;
    use WithFaker;
    use WithPromo;

    /**
     * User can update a promo test
     *
     * @return void
     */
    public function test_user_can_update_promo() : void
    {
        $user = User::first()?? User::factory()->create();
        $this->actingAs($user);

        $this->promo->name = $this->faker()->unique()->name();

        Event::fake();
        $response = $this->patch(route('api.promos.update', $this->promo), $this->promo->toArray());
        $this->promo->refresh();

        $response->assertValid();
        $response->assertOk();
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas($this->promo::class, $this->promo->only($this->promo->getFillable()));
        Event::assertDispatched(\App\Events\Promo\PromoUpdated::class);
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
