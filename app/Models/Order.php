<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
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
        'vat',
        'total_price',
        'status',
        'ordered_at',
        'shipped_at',
        'delivered_at',
        'failed_at',
        'failure_reason'
    ];

     /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'is_paid',
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
    public function payment() : MorphOne
    {
        return $this->morphOne(Payment::class, 'paymentable');
    }

      /**
     *  meta attribute
     */
    protected function isPaid(): Attribute
    {
        return Attribute::make(
            get: fn ()=> !is_null($this->payment) && !is_null($this->payment->paid_at?? null) && !is_null($this->payment->verified_at ?? null),
        );
    }

    public static function genNumber()
    {
        $prefix = 'sf';
        $today = date('dmY');
        $time = date('His');

        $day_number = Order::where('number', 'like', '%'.$prefix.$today.'%')->count();
        $overall_number = Order::count()+1;
        
        return $prefix.$today.$time.'dn'.$day_number.'ov'.$overall_number;
    }

}
