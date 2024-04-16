<?php

namespace Database\Seeders;


use App\Models\Category;
use App\Models\Image;
use App\Models\Product;
use App\Models\Role;
use App\Models\Store;
use App\Models\User;
use App\Services\CloudinaryService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $sanmtos_email = 'store-admin@sanmtos.com';
        $sanmtos_vendor = User::where('email', $sanmtos_email)->first();

        if(is_null($sanmtos_vendor))
        {
            $sanmtos_vendor = User::factory()->create([
                'email' => $sanmtos_email,
            ]);

             // create a store admin role if not exist
            $admin_role = Role::firstOrCreate([
                'name' => 'store-admin',
                'store_id'=> null
            ]);

            //assign admin role  to admin user
            if(empty($sanmtos_vendor->roles()->where('role_id', $admin_role->id)->first())){
                $sanmtos_vendor->roles()->attach($admin_role->id);
            }
        }
       
        
        $santoms_store =  $sanmtos_vendor->store;

        if(is_null($santoms_store))
        {
            $santoms_store = Store::factory()
            ->hasInCoupons(2)
            ->hasInPromos(2)
            ->hasImages(1)
            ->create([
                'name' => 'Sanmtos Farm',
                'user_id' => $sanmtos_vendor->id,
                'description' => 'Sanmtos Farm - Bringing it closer to you'
            ]);           
        }
        
        $folder = 'sanmtos_sample_products';
        $files = Storage::files($folder);
        $products = [];
        $images = [];
        $likes = [];

        foreach ($files as $key => $file) {

            $file_name = Str::remove($folder.'/', $file);
            $exploded = explode('_', $file_name);
            
            try {

                $category_name = Str::before($exploded[2], '.');
                $prod_name = $exploded[0];
                $prod_price = $exploded[1];
 
                $category = Category::firstOrCreate([
                    'name' => $category_name,
                    'slug' => Str::slug($category_name),
                ]);

                $product = Product::where('name',$prod_name)
                ->where( 'store_id', $santoms_store->id)->first();
                
                if(is_null($product))
                {
                    $product = Product::create(
                        Product::factory()->make([
                            'name'=> $prod_name,
                            'price' => $prod_price,
                            'regular_price' => $prod_price + \random_int(10,3000),
                            'category_id' => $category->id,
                            'weight' => 0.5,
                            'volume' => random_int(1,3),
                            'store_id' => $santoms_store->id,
                        ])->toArray()
                    );
                        
                }
                else {
                    
                }
                    
                // add product to inventory
                // $product->inventories()->create([
                //     'quantity'=> 100,
                //     'shop_id' => $santoms_store->id,
                // ]);

                $product->likes()->createMany([
                    ['user_id' => User::inRandomOrder()->first()->id?? User::factory()->create()->id,],
                    ['user_id' => User::inRandomOrder()->first()->id?? User::factory()->create()->id,],
                    ['user_id' => User::inRandomOrder()->first()->id?? User::factory()->create()->id,],
                    ['user_id' => User::inRandomOrder()->first()->id?? User::factory()->create()->id,],
                ]);

                $product->ratings()->createMany([
                    [
                        'user_id' => User::inRandomOrder()->first()->id?? User::factory()->create()->id,
                        'stars' => random_int(1,5),
                    ],
                    [
                        'user_id' => User::inRandomOrder()->first()->id?? User::factory()->create()->id,
                        'stars' => random_int(1,5),
                    ],
                    [
                        'user_id' => User::inRandomOrder()->first()->id?? User::factory()->create()->id,
                        'stars' => random_int(1,5),
                    ],
                    [
                        'user_id' => User::inRandomOrder()->first()->id?? User::factory()->create()->id,
                        'stars' => random_int(1,5),
                    ],
                ]);

                // CloudinaryService::
               
                $file = new File(storage_path('app/'.$file));

                $options = [
                    'overlayImageURL' => null, //
                    'thumbnail' => true, //true or false
                    'dimensions' => null, // null or ['width'=>700, 'height'=>700]
                    'roundCorners' => 0,
                ];


                // upload to cloudinary
                $uploaded_image = CloudinaryService::uploadImage($file, $path ='products/', $options);

                // save image information
                $image_data['url'] = $uploaded_image->getSecurePath();
                $image_data['imageable_id'] = $product->id;
                $image_data['imageable_type'] = $product::class;

                Image::create($image_data);
               
            } catch (\Throwable $th) {
               print(PHP_EOL.PHP_EOL.$th->getMessage().PHP_EOL.PHP_EOL);
            }
        }
        // Store::factory()
        // ->count(20)
        // ->hasInCoupons(2)
        // ->hasInPromos(2)
        // ->hasImages(1)
        // ->create();
    }
}
