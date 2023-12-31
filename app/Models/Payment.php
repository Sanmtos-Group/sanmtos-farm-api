<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

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
        'paymentable_id',
        'paymentable_type',
        'payment_gateway_id',
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

}
