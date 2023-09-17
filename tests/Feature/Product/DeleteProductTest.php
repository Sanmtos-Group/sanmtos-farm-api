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

class DeleteProductTest extends TestCase
{
    use FastRefreshDatabase;
    use WithFaker;
    use WithProduct;

    /**
     * User can update a product test
     *
     * @return void
     */
    public function test_user_can_delete_product() : void
    {
        $user = User::first()?? User::factory()->create();
        $this->actingAs($user);

        Event::fake();
        $response = $this->delete(route('api.products.destroy', $this->product));
        $this->product->refresh();
        $response->assertValid();
        $response->assertOk();
        $response->assertSessionHasNoErrors();
        $this->assertSoftDeleted($this->product);
        $this->assertDatabaseHas($this->product::class, $this->product->only($this->product->getFillable()));
        Event::assertDispatched(\App\Events\Product\ProductTrashed::class);
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
