<?php
namespace App\Enums;

use  App\Traits\Enum\HasEnumStaticMethods;

enum VATEnum:string
{
    use HasEnumStaticMethods;
    
    case Name = 'Value Added Tax (VAT)';
    case Key = 'VAT';
    case Value = "5" ;
    case Description = "Value Added Tax (%) to be added";
}