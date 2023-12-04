<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    use HasFactory;
    use HasUuids;
    
     /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => \App\Events\Address\AddressCreated::class,
        'deleted' => \App\Events\Address\AddressDeleted::class,
        'restored' => \App\Events\Address\AddressRestored::class,
        'updated' => \App\Events\Address\AddressUpdated::class,
        'trashed' => \App\Events\Address\AddressTrashed::class,
    ];
    
     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name' ,
        'dialing_code' ,
        'phone_number',
        'address',
        'zip_code',
        'country_id', 
        'state',
        'lga',
        'addressable_id',
        'addressable_type',
        'is_preferred',
    ];

    /**
     * The attributes that should be hidden in arrays.
     *
     * @var array
     */
    protected $hidden = ['addressable_id', 'addressable_type'];

    /**
     * Get the parent addressable model (user or store).
     */
    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the country that the address is.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
