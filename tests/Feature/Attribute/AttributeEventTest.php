<?php

namespace Tests\Feature\Attribute;

Use App\Traits\Testing\WithAttribute;
Use App\Traits\Testing\FastRefreshDatabase;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\Fluent\AssertableJson;

use Tests\TestCase;

class AttributeEventTest extends TestCase
{
    use FastRefreshDatabase;
    use WithAttribute;

    /**
     * An attribute create event can be dispatched
     *
     * @return void
     */
    public function test_attribute_create_event_can_be_dispatched()
    {
        Event::fake();
        \App\Events\Attribute\AttributeCreated::dispatch($this->attribute);
        Event::assertDispatched(\App\Events\Attribute\AttributeCreated::class);
    }

    /**
     * An attribute update event can be dispatched
     *
     * @return void
     */
    public function test_attribute_update_event_can_be_dispatched()
    {
        Event::fake();
        \App\Events\Attribute\AttributeUpdated::dispatch($this->attribute);
        Event::assertDispatched(\App\Events\Attribute\AttributeUpdated::class);
    }

    /**
     * An attribute trash event can be dispatched
     *
     * @return void
     */
    public function test_attribute_trash_event_can_be_dispatched()
    {
        Event::fake();
        \App\Events\Attribute\AttributeTrashed::dispatch($this->attribute);
        Event::assertDispatched(\App\Events\Attribute\AttributeTrashed::class);
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
