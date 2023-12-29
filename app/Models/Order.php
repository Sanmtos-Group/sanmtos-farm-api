<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Model;
class Order extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'number',
        'user_id',
        'address_id',
        'delivery_fee',
        'price', 
        'coupon_id',
        'promo_id',
        'total_price',
        'status',
        'ordered_at',
        'shipped_at',
        'delivered_at',
        'failed_at',
        'failure_reason'
    ];


    /**
     * Get the user that owns the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the delivery address for the order.
     */
    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'address_id');
    }

    /**
     * Get all of the orderables for the order.
     */
    public function orderables(): HasMany
    {
        return $this->hasMany(Orderable::class);
    }

     /**
     * Get all of the model's payments.
     */
    public function payments() : MorphMany
    {
        return $this->morphMany(Payment::class, 'paymentable');
    }

}
