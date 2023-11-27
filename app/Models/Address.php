<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;
    use HasUuids;

     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
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
     * Get the parent addressable model (user or store).
     */
    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }
}
