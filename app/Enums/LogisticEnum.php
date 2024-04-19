<?php
namespace App\Enums;

use  App\Traits\Enum\HasEnumStaticMethods;

enum LogisticEnum:string
{
    use HasEnumStaticMethods;
    
    case DHL = 'DHL';
}