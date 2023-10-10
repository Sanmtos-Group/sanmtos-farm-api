<?php
namespace App\Traits\Testing; 
use App\Models\Promo;

trait WithPromo {

    /**
     * The promo instance.
     *
     * @var \App\Models\Promo
     */
    protected $promo;

    /**
     * Setup up a new promo instance.
     *
     * @return \App\Models\Promo
     */
    protected function setUpPromo(): void
    {
        $this->promo = Promo::factory()->create();
    }

    /**
     * @return \App\Models\Promo
     */
    protected function makePromo($promo_data = null ): Promo
    {
        return is_array($promo_data) ? Promo::factory()->make($promo_data) : Promo::factory()->make() ;   
    }

     /**
     * Get the promo instance for a given data.
     *
     * @param  array<string ,*>|null  $promo_data
     * 
     * @return \App\Models\Promo
     */
    public function promo($promo_data = null ): Promo
    {
        $promo = is_array($promo_data) ? Promo::firstOrCreate(Promo::factory()->make($promo_data)->toArray()) : Promo::first();
        return $promo ?? Promo::factory()->create();
    }

    /**
     * Get a trashed promo data.
     *
     * @return \App\Models\Promo
     */
    public function PromoTrashed(): Promo 
    {
        $promo_trashed = Promo::onlyTrashed()->get()->first();
        if($promo_trashed)
            return  $promo_trashed;
            
        $promo_trashed = $this->promo();
        $promo_trashed->delete();
        return $promo_trashed;
    }

}