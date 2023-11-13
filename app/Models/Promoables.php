<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Promoables extends Pivot
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'promo_id',
        'promoable_id',
        'promoable_type',
    ];
}
