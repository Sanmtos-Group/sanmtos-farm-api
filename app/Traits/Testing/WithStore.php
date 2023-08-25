<?php
namespace App\Traits\Testing; 
use App\Models\Store;

trait WithStore {

    /**
     * The store instance.
     *
     * @var \App\Models\Store
     */
    protected $store;

    /**
     * Setup up a new store instance.
     *
     * @return \App\Models\Store
     */
    protected function setUpStore(): void
    {
        $this->store = Store::factory()->create();
    }

    /**
     * @return \App\Models\Store
     */
    protected function makeStore($store_data = null): Store
    {
        return is_array($store_data) ? Store::factory()->make($store_data) : Store::factory()->make() ;   
    }

     /**
     * Get the store instance for a given data.
     *
     * @param  array<string ,*>|null  $store_data
     * 
     * @return \App\Models\Store
     */
    public function store($store_data = null ): Store
    {
        $store = is_array($store_data) ? Store::firstOrCreate(Store::factory()->make($store_data)->toArray()) : Store::first();
        return $store ?? Store::factory()->create();
    }

    /**
     * Get a trashed store data.
     *
     * @return \App\Models\Store
     */
    public function storeTrashed(): Store 
    {
        $store_trashed = Store::onlyTrashed()->get()->first();
        if($store_trashed)
            return  $store_trashed;
            
        $store_trashed = $this->store();
        $store_trashed->delete();
        return $store_trashed;
    }

}