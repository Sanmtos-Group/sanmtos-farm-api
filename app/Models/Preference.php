<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Preference extends Model
{
    use HasFactory;
    use HasUuids;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'language_code',
        'currency_code',
        'preferenceable_id',
        'preferenceable_type',
    ];


     /**
     * The attributes that should be hidden in arrays.
     *
     * @var array
     */
    protected $hidden = ['preferenceable_id', 'preferenceable_type'];

    /**
     * Get the parent prefercenable model (user or store).
     */
    public function preferenceable() : MorphTo
    {
        return $this->morphTo();
    }
}
