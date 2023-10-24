<?php

namespace Tests\Feature\Product;

use App\Models\User;
use App\Models\Image;
use App\Models\Promo;
Use App\Traits\Testing\WithProduct;
Use App\Traits\Testing\FastRefreshDatabase;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

use Tests\TestCase;

class ProductRelationshipTest extends TestCase
{
    use FastRefreshDatabase;
    use WithFaker;
    use WithProduct;

    /**
     * Product belongs to a store
     *
     * @return void
     */
    public function test_product_belongs_to_a_store() : void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $store = $this->product->store;

        $this->assertModelExists($store);
        $this->assertInstanceOf(\App\Models\Store::class, $store);
        $this->assertDatabaseHas($store::class, $store->only($store->getFillable()));
    }


    /**
     * Product belongs to a category
     *
     * @return void
     */
    public function test_product_belongs_to_a_category() : void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $category = $this->product->category;

        $this->assertModelExists($category);
        $this->assertInstanceOf(\App\Models\Category::class, $category);
        $this->assertDatabaseHas($category::class, $category->only($category->getFillable()));
    }

    /**
     * Product has many images
     *
     * @return void
     */
    public function test_product_has_many_images() : void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $image_1 = Image::factory([
            'imageable_id' => $this->product->id,
            'imageable_type' => $this->product::class,
        ])->create();

        $image_2 = Image::factory([
            'imageable_id' => $this->product->id,
            'imageable_type' => $this->product::class,
        ])->create();

        $images = $this->product->images;

        $this->assertTrue(!is_null($images));
        $this->assertEquals(2, $images->count());
        
        $this->assertModelExists($image_1);
        $this->assertModelExists($image_2);
 
        $this->assertInstanceOf(\App\Models\Image::class, $image_1);
        $this->assertInstanceOf(\App\Models\Image::class, $image_2);

        $this->assertDatabaseHas($image_1::class, $image_1->only($image_1->getFillable()));
        $this->assertDatabaseHas($image_2::class, $image_2->only($image_2->getFillable()));
    }

    /**
     * Product has many promos
     *
     * @return void
     */
    public function test_product_has_many_promos() : void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $promo_1 = Promo::factory()->create();

        $promo_2 = Promo::factory()->create();

        $this->product->promos()->syncWithoutDetaching($promo_1);
        $this->product->promos()->syncWithoutDetaching($promo_2);


        $promos = $this->product->promos;

        $this->assertTrue(!is_null($promos));
        $this->assertEquals(2, $promos->count());
        
        $this->assertModelExists($promo_1);
        $this->assertModelExists($promo_2);
 
        $this->assertInstanceOf(\App\Models\Promo::class, $promo_1);
        $this->assertInstanceOf(\App\Models\Promo::class, $promo_2);

        $this->assertDatabaseHas($promo_1::class, $promo_1->only($promo_1->getFillable()));
        $this->assertDatabaseHas($promo_2::class, $promo_2->only($promo_2->getFillable()));
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
