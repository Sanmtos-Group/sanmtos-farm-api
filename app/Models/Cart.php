<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Cart extends Model
{
    use HasFactory;
    use HasUuids;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
    ];
    
      /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['id','user_id'];

     /**
     * Determine  a cart item total price
     */
    protected function totalPrice(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->price * $this->quantity,
            set: fn () => $this->price * $this->quantity,
        );
    } 
}
