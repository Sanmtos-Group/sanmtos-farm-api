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

class UpdateAttributeTest extends TestCase
{
    use FastRefreshDatabase;
    use WithFaker;
    use WithAttribute;

    /**
     * User can update a attribute test
     *
     * @return void
     */
    public function test_user_can_update_attribute() : void
    {
        $user = User::first()?? User::factory()->create();
        $this->actingAs($user);

        $this->attribute->name = $this->faker()->unique()->name();

        Event::fake();
        $response = $this->patch(route('api.attributes.update', $this->attribute), $this->attribute->toArray());
        $this->attribute->refresh();

        $response->assertValid();
        $response->assertOk();
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas($this->attribute::class, $this->attribute->only($this->attribute->getFillable()));
        Event::assertDispatched(\App\Events\Attribute\AttributeUpdated::class);
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
