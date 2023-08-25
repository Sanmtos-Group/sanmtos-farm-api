<?php

namespace Tests\Feature\Store;

Use App\Traits\Testing\WithStore;
Use App\Traits\Testing\FastRefreshDatabase;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\Fluent\AssertableJson;

use Tests\TestCase;

class StoreEventTest extends TestCase
{
    use FastRefreshDatabase;
    use WithStore;

    /**
     * An store create event can be dispatched
     *
     * @return void
     */
    public function test_store_create_event_can_be_dispatched()
    {
        Event::fake();
        \App\Events\Store\StoreCreated::dispatch($this->store);
        Event::assertDispatched(\App\Events\Store\StoreCreated::class);
    }

    /**
     * An store update event can be dispatched
     *
     * @return void
     */
    public function test_store_update_event_can_be_dispatched()
    {
        Event::fake();
        \App\Events\Store\StoreUpdated::dispatch($this->store);
        Event::assertDispatched(\App\Events\Store\StoreUpdated::class);
    }

    /**
     * An store trash event can be dispatched
     *
     * @return void
     */
    public function test_store_trash_event_can_be_dispatched()
    {
        Event::fake();
        \App\Events\Store\StoreTrashed::dispatch($this->store);
        Event::assertDispatched(\App\Events\Store\StoreTrashed::class);
    }


    /**
     * Setup store test environment.
     * 
     * @override Illuminate\Foundation\Testing\TestCase  setUp()
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpStore();
    }
}
