<?php
namespace App\Enums;

use  App\Traits\Enum\HasEnumStaticMethods;

enum HtmlFormElementEnum: string 
{
    use HasEnumStaticMethods;

    case Input = 'input';
    case TextArea = 'textarea';
    case Select = 'select';
    case Datalist = 'datalist';  
}