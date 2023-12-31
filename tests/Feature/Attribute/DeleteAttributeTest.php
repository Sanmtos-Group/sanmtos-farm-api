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

class DeleteAttributeTest extends TestCase
{
    use FastRefreshDatabase;
    use WithFaker;
    use WithAttribute;

    /**
     * User can update a attribute test
     *
     * @return void
     */
    public function test_user_can_delete_attribute() : void
    {
        $user = User::first()?? User::factory()->create();
        $this->actingAs($user);

        Event::fake();
        $response = $this->delete(route('api.attributes.destroy', $this->attribute));
        $this->attribute->refresh();
        $response->assertValid();
        $response->assertOk();
        $response->assertSessionHasNoErrors();
        $this->assertSoftDeleted($this->attribute);
        $this->assertDatabaseHas($this->attribute::class, $this->attribute->only($this->attribute->getFillable()));
        Event::assertDispatched(\App\Events\Attribute\AttributeTrashed::class);
        $response->assertJson(fn (AssertableJson $json) =>$json->etc());
    }

    /**
     * User can update a attribute test
     *
     * @return void
     */
    public function test_user_can_restore_deleted_attribute() : void
    {
        $user = User::first()?? User::factory()->create();
        $this->actingAs($user);

        Event::fake();
        $trashed_attribute = $this->attributeTrashed();
        $response = $this->patch(route('api.attributes.restore', $trashed_attribute));
        $trashed_attribute->refresh();
        $response->assertValid();
        $response->assertOk();
        $response->assertSessionHasNoErrors();
        $this->assertNotSoftDeleted($trashed_attribute);
        $this->assertDatabaseHas($trashed_attribute::class, $trashed_attribute->only($trashed_attribute->getFillable()));
        Event::assertDispatched(\App\Events\Attribute\AttributeRestored::class);
        $response->assertJson(fn (AssertableJson $json) =>$json->etc());
    }

    /**
     * User can update a attribute test
     *
     * @return void
     */
    public function test_user_can_force_delete_attribute() : void
    {
        $user = User::first()?? User::factory()->create();
        $this->actingAs($user);

        Event::fake();
        $response = $this->delete(route('api.attributes.forceDestroy', $this->attribute));
        $response->assertValid();
        $response->assertOk();
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseMissing($this->attribute::class, $this->attribute->only($this->attribute->getFillable()));
        $this->assertModelMissing($this->attribute);
        Event::assertDispatched(\App\Events\Attribute\AttributeDeleted::class);
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
