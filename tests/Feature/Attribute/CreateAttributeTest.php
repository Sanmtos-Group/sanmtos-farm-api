<?php

namespace Tests\Feature\Attribute;

use App\Models\User;
Use App\Traits\Testing\WithAttribute;
Use App\Traits\Testing\FastRefreshDatabase;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\Fluent\AssertableJson;

use Tests\TestCase;

class CreateAttributeTest extends TestCase
{
    use FastRefreshDatabase;
    use WithFaker;
    use WithAttribute;

    /**
     * User can create a attribute test
     *
     * @return void
     */
    public function test_user_can_create_attribute() : void
    {
        $user = User::factory()->create();
        $this->actingAs($user); 
               
        Event::fake();
        $attribute = $this->makeAttribute();
        $response = $this->post(route('api.attributes.store'), $attribute->toArray());

        $response->assertValid();
        $response->assertSuccessful();
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas($attribute::class, $attribute->only($attribute->getFillable()));
        Event::assertDispatched(\App\Events\Attribute\AttributeCreated::class);
        $response->assertJson(fn (AssertableJson $json) =>$json->etc());
    }


    /**
     * Setup attribute test environment.
     * 
     * @override Illuminate\Foundation\Testing\TestCase  setUp()
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpAttribute();
    }
}
