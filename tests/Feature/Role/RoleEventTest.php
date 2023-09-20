<?php

namespace Tests\Feature\Role;

Use App\Traits\Testing\WithRole;
Use App\Traits\Testing\FastRefreshDatabase;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\Fluent\AssertableJson;

use Tests\TestCase;

class RoleEventTest extends TestCase
{
    use FastRefreshDatabase;
    use WithRole;

    /**
     * An role create event can be dispatched
     *
     * @return void
     */
    public function test_role_create_event_can_be_dispatched()
    {
        Event::fake();
        \App\Events\Role\RoleCreated::dispatch($this->role);
        Event::assertDispatched(\App\Events\Role\RoleCreated::class);
    }

    /**
     * An role update event can be dispatched
     *
     * @return void
     */
    public function test_role_update_event_can_be_dispatched()
    {
        Event::fake();
        \App\Events\Role\RoleUpdated::dispatch($this->role);
        Event::assertDispatched(\App\Events\Role\RoleUpdated::class);
    }

    /**
     * An role trash event can be dispatched
     *
     * @return void
     */
    public function test_role_trash_event_can_be_dispatched()
    {
        Event::fake();
        \App\Events\Role\RoleDeleted::dispatch($this->role);
        Event::assertDispatched(\App\Events\Role\RoleDeleted::class);
    }


    /**
     * Setup role test environment.
     * 
     * @override Illuminate\Foundation\Testing\TestCase  setUp()
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpRole();
    }
}
