<?php
namespace App\Enums;

use  App\Traits\Enum\HasEnumStaticMethods;

enum HtmlInputTypeEnum: string 
{
    use HasEnumStaticMethods;

    case Text = 'text';
    case Password = 'password';
    case Email = 'email';
    case Number = 'number';
    case Date = 'date';
    case Time = 'time';
    case Url = 'url';
    case Search = 'search';
    case Tel = 'tel';
    case Color = 'color';
    case Checkbox = 'checkbox';
    case Radio = 'radio';
    case File = 'file';
    case Range = 'range';
    case Hidden = 'hidden';
    case Image = 'image';
    case Submit = 'submit';
    case Reset = 'reset';
    case Button = 'button';
    case Week = 'week';
    case Month = 'month';
    case Datetime = 'datetime';
    case DatetimeLocal = 'datetime-local';

  
}