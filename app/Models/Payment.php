<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;
class Payment extends Model
{
    use HasFactory;
    use HasUuids;
    
    public const GATEWAYS = [
        'paystack',
        'flutterwave',
        'paypal',
    ];
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id', 
        'amount',
        'transaction_reference',
        'paymentable_id',
        'paymentable_type',
        'gateway_id',
        'method',
        'currency',
        'ip_address',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [

    ];

    /**
     * Get user making the payment
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the payment gateway of the model
     */
    public function gateway()
    {
        return $this->belongsTo(PaymentGateway::class, 'gateway_id');
    }

    /**
     * Get the parent paymentable model (order or subcription).
     */
    public function paymentable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Determine  a cart item  metadata
     */
    protected function metadata(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value) ,
        );
    } 

    public static function genTranxRef(){
        return 'sf'.Str::of(Str::uuid())->replace("-","");
    }

}
