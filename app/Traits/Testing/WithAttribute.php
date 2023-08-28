<?php
namespace App\Traits\Testing; 
use App\Models\Attribute;

trait WithAttribute {

    /**
     * The attribute instance.
     *
     * @var \App\Models\Attribute
     */
    protected $attribute;

    /**
     * Setup up a new attribute instance.
     *
     * @return \App\Models\Attribute
     */
    protected function setUpAttribute(): void
    {
        $this->attribute = Attribute::factory()->create();
    }

    /**
     * @return \App\Models\Attribute
     */
    protected function makeAttribute($attribute_data = null): Attribute
    {
        return is_array($attribute_data) ? Attribute::factory()->make($attribute_data) : Attribute::factory()->make() ;   
    }

     /**
     * Get the attribute instance for a given data.
     *
     * @param  array<string ,*>|null  $attribute_data
     * 
     * @return \App\Models\Attribute
     */
    public function attribute($attribute_data = null ): Attribute
    {
        $attribute = is_array($attribute_data) ? Attribute::firstOrCreate(Attribute::factory()->make($attribute_data)->toArray()) : Attribute::first();
        return $attribute ?? Attribute::factory()->create();
    }

    /**
     * Get a trashed attribute data.
     *
     * @return \App\Models\Attribute
     */
    public function attributeTrashed(): Attribute 
    {
        $attribute_trashed = Attribute::onlyTrashed()->get()->first();
        if($attribute_trashed)
            return  $attribute_trashed;
            
        $attribute_trashed = $this->attribute();
        $attribute_trashed->delete();
        return $attribute_trashed;
    }

}