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

class DeleteImageTest extends TestCase
{
    use FastRefreshDatabase;
    use WithFaker;
    use WithImage;

    /**
     * User can update a image test
     *
     * @return void
     */
    public function test_user_can_delete_image() : void
    {
        $user = User::first()?? User::factory()->create();
        $this->actingAs($user);

        Event::fake();
        $response = $this->delete(route('api.images.destroy', $this->image));
        $this->image->refresh();
        $response->assertValid();
        $response->assertOk();
        $response->assertSessionHasNoErrors();
        $this->assertSoftDeleted($this->image);
        $this->assertDatabaseHas($this->image::class, $this->image->only($this->image->getFillable()));
        Event::assertDispatched(\App\Events\Image\ImageTrashed::class);
        $response->assertJson(fn (AssertableJson $json) =>$json->etc());
    }

    /**
     * User can update a image test
     *
     * @return void
     */
    public function test_user_can_restore_deleted_image() : void
    {
        $user = User::first()?? User::factory()->create();
        $this->actingAs($user);

        Event::fake();
        $trashed_image = $this->imageTrashed();
        $response = $this->patch(route('api.images.restore', $trashed_image));
        $trashed_image->refresh();
        $response->assertValid();
        $response->assertOk();
        $response->assertSessionHasNoErrors();
        $this->assertNotSoftDeleted($trashed_image);
        $this->assertDatabaseHas($trashed_image::class, $trashed_image->only($trashed_image->getFillable()));
        Event::assertDispatched(\App\Events\Image\ImageRestored::class);
        $response->assertJson(fn (AssertableJson $json) =>$json->etc());
    }

    /**
     * User can update a image test
     *
     * @return void
     */
    public function test_user_can_force_delete_image() : void
    {
        $user = User::first()?? User::factory()->create();
        $this->actingAs($user);

        Event::fake();
        $response = $this->delete(route('api.images.forceDestroy', $this->image));
        $response->assertValid();
        $response->assertOk();
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseMissing($this->image::class, $this->image->only($this->image->getFillable()));
        $this->assertModelMissing($this->image);
        Event::assertDispatched(\App\Events\Image\ImageDeleted::class);
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
