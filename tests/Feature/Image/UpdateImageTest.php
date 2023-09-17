<?php

namespace Tests\Feature\Image;

use App\Models\User;
Use App\Traits\Testing\WithImage;
Use App\Traits\Testing\FastRefreshDatabase;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\Fluent\AssertableJson;

use Tests\TestCase;

class UpdateImageTest extends TestCase
{
    use FastRefreshDatabase;
    use WithFaker;
    use WithImage;

    /**
     * User can update a image test
     *
     * @return void
     */
    public function test_user_can_update_image() : void
    {
        $user = User::first()?? User::factory()->create();
        $this->actingAs($user);

        $this->image->url= $this->faker()->imageUrl();

        Event::fake();
        $response = $this->patch(route('api.images.update', $this->image), $this->image->toArray());
        $this->image->refresh();

        $response->assertValid();
        $response->assertOk();
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas($this->image::class, $this->image->only($this->image->getFillable()));
        Event::assertDispatched(\App\Events\Image\ImageUpdated::class);
        $response->assertJson(fn (AssertableJson $json) =>$json->etc());
    }


    /**
     * Setup image test environment.
     * 
     * @override Illuminate\Foundation\Testing\TestCase  setUp()
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpImage();
    }
}
