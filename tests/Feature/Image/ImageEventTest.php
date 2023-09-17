<?php

namespace Tests\Feature\Image;

Use App\Traits\Testing\WithImage;
Use App\Traits\Testing\FastRefreshDatabase;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\Fluent\AssertableJson;

use Tests\TestCase;

class ImageEventTest extends TestCase
{
    use FastRefreshDatabase;
    use WithImage;

    /**
     * An image create event can be dispatched
     *
     * @return void
     */
    public function test_image_create_event_can_be_dispatched()
    {
        Event::fake();
        \App\Events\Image\ImageCreated::dispatch($this->image);
        Event::assertDispatched(\App\Events\Image\ImageCreated::class);
    }

    /**
     * An image update event can be dispatched
     *
     * @return void
     */
    public function test_image_update_event_can_be_dispatched()
    {
        Event::fake();
        \App\Events\Image\ImageUpdated::dispatch($this->image);
        Event::assertDispatched(\App\Events\Image\ImageUpdated::class);
    }

    /**
     * An image delete event can be dispatched
     *
     * @return void
     */
    public function test_image_delete_event_can_be_dispatched()
    {
        Event::fake();
        \App\Events\Image\ImageDeleted::dispatch($this->image);
        Event::assertDispatched(\App\Events\Image\ImageDeleted::class);
    }

    /**
     * An image trash event can be dispatched
     *
     * @return void
     */
    public function test_image_trash_event_can_be_dispatched()
    {
        Event::fake();
        \App\Events\Image\ImageTrashed::dispatch($this->image);
        Event::assertDispatched(\App\Events\Image\ImageTrashed::class);
    }

    /**
     * An image restore event can be dispatched
     *
     * @return void
     */
    public function test_image_restore_event_can_be_dispatched()
    {
        Event::fake();
        \App\Events\Image\ImageRestored::dispatch($this->image);
        Event::assertDispatched(\App\Events\Image\ImageRestored::class);
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
