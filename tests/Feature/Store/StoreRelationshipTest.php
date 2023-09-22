<?php

namespace Tests\Feature\Store;

Use App\Traits\Testing\WithProduct;
Use App\Traits\Testing\WithStore;
Use App\Traits\Testing\WithUser;
Use App\Traits\Testing\FastRefreshDatabase;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\Fluent\AssertableJson;

use Tests\TestCase;

class StoreRelationshipTest extends TestCase
{
    use FastRefreshDatabase;
    use WithFaker;
    use WithProduct;
    use WithStore;
    use WithUser;

    /**
     * Store belongs to an owner test
     *
     * @return void
     */
    public function test_store_belongs_to_an_owner() : void
    {
        $this->actingAs($this->user);


        Event::fake();

        $this->assertDatabaseHas($this->store::class, $this->store->toArray());
        $this->assertEquals(1, $this->store->user()->count());

        $store_owner = $this->store->user;
        $this->assertInstanceOf(\App\Models\User::class, $store_owner);
        $this->assertDatabaseHas($store_owner::class, $store_owner->only($store_owner->getFillable()));
        $this->assertEquals($this->store->id, $store_owner->store->id);

    }

    /**
     * Store has many products test
     *
     * @return void
     */
    public function test_store_can_have_many_products() : void
    {
        $this->actingAs($this->user);

        Event::fake();
        $this->assertDatabaseHas($this->store::class, $this->store->toArray());

        $product_1 = $this->product(['store_id'=>$this->store->id]);
        $product_2 = $this->product(['store_id'=>$this->store->id]);

        $this->assertInstanceOf(\App\Models\Product::class, $product_1);
        $this->assertInstanceOf(\App\Models\Product::class, $product_2);

        $this->assertDatabaseHas($product_1::class, $product_1->toArray());
        $this->assertDatabaseHas($product_2::class, $product_2->toArray());

        $this->assertGreaterThanOrEqual(2, $this->store->products->count());

    }

    /**
     * Store has many staffs test
     *
     * @return void
     */
    public function test_store_can_have_many_staffs() : void
    {
        $this->actingAs($this->user);
        Event::fake();

        $this->assertInstanceOf(\App\Models\User::class, $this->user);
        $this->assertDatabaseHas($this->user::class, $this->user->only($this->user->getFillable()));

        $user2 = $this->user();
        $this->assertInstanceOf(\App\Models\User::class, $user2);
        $this->assertDatabaseHas($user2::class, $user2->only($user2->getFillable()));

        $this->store->staffs()->attach($this->user->id);
        $this->store->staffs()->attach($user2->id);

        $this->assertGreaterThanOrEqual(2, $this->store->staffs->count());

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
        $this->setUpUser();
        $this->setUpStore();
        $this->setUpProduct();

    }
}
