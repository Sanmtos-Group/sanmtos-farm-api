<?php
namespace App\Enums;

use  App\Traits\Enum\HasEnumStaticMethods;

enum VatEnum:string
{
    use HasEnumStaticMethods;

    case Key = 'vat';
    case Value = 5.00 ;
}