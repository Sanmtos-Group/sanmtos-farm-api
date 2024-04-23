<?php
namespace App\Enums;

use  App\Traits\Enum\HasEnumStaticMethods;

enum PaymentGatewayEnum:string
{
    use HasEnumStaticMethods;
    
    case Paystack = 'Paystack';
    case Flutterwave = 'Flutterwave';
    case Paypal = 'Paypal';
}