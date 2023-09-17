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

class CreateImageTest extends TestCase
{
    use FastRefreshDatabase;
    use WithFaker;
    use WithImage;

    /**
     * User can create a image test
     *
     * @return void
     */
    public function test_user_can_create_image() : void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Event::fake();
        $image = $this->makeImage();
        $response = $this->post(route('api.images.store'), $image->toArray());

        $response->assertValid();
        $response->assertSuccessful();
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas($image::class, $image->only($image->getFillable()));
        Event::assertDispatched(\App\Events\Image\ImageCreated::class);
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
