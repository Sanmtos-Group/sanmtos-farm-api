<?php
namespace App\Enums;

use  App\Traits\Enum\HasEnumStaticMethods;

enum DiscountTypeEnum:string
{
    use HasEnumStaticMethods;

    case FlatOff = 'FLAT_OFF';
    case PercentageOff = 'PERCENTAGE_OFF';
}