<?php
namespace App\Enums;

use  App\Traits\Enum\HasEnumStaticMethods;

enum TaskTypeEnum:string
{
    use HasEnumStaticMethods;
    
    case INSERT = 'insert';
    case EDIT = 'edit';
}