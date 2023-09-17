<?php

namespace Tests\Feature\Product;

Use App\Traits\Testing\WithProduct;
Use App\Traits\Testing\FastRefreshDatabase;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\Fluent\AssertableJson;

use Tests\TestCase;

class ProductEventTest extends TestCase
{
    use FastRefreshDatabase;
    use WithProduct;

    /**
     * An product create event can be dispatched
     *
     * @return void
     */
    public function test_product_create_event_can_be_dispatched()
    {
        Event::fake();
        \App\Events\Product\ProductCreated::dispatch($this->product);
        Event::assertDispatched(\App\Events\Product\ProductCreated::class);
    }

    /**
     * An product update event can be dispatched
     *
     * @return void
     */
    public function test_product_update_event_can_be_dispatched()
    {
        Event::fake();
        \App\Events\Product\ProductUpdated::dispatch($this->product);
        Event::assertDispatched(\App\Events\Product\ProductUpdated::class);
    }

    /**
     * An product trash event can be dispatched
     *
     * @return void
     */
    public function test_product_trash_event_can_be_dispatched()
    {
        Event::fake();
        \App\Events\Product\ProductTrashed::dispatch($this->product);
        Event::assertDispatched(\App\Events\Product\ProductTrashed::class);
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
