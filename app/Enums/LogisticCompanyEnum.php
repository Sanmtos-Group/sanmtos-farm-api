<?php
namespace App\Enums;

use  App\Traits\Enum\HasEnumStaticMethods;

enum LogisticCompanyEnum:string
{
    use HasEnumStaticMethods;
    
    case DHL = 'DHL';
}