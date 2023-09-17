<?php

namespace Tests\Feature\Product;

use App\Models\User;
Use App\Traits\Testing\WithProduct;
Use App\Traits\Testing\FastRefreshDatabase;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\Fluent\AssertableJson;

use Tests\TestCase;

class CreateProductTest extends TestCase
{
    use FastRefreshDatabase;
    use WithFaker;
    use WithProduct;

    /**
     * User can create a product test
     *
     * @return void
     */
    public function test_user_can_create_product() : void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Event::fake();
        $products = $this->makeProduct();
        $response = $this->post(route('api.products.store'), $products->toArray());

        $response->assertValid();
        $response->assertSuccessful();
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas($products::class, $products->only($products->getFillable()));
        Event::assertDispatched(\App\Events\Product\ProductCreated::class);
        $response->assertJson(fn (AssertableJson $json) =>$json->etc());
    }


    /**
     * Setup product test environment.
     * 
     * @override Illuminate\Foundation\Testing\TestCase  setUp()
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpProduct();
    }
}
