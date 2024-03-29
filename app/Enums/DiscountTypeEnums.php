<?php
namespace App\Enums;

enum DiscountTypeEnums:string
{
    case FlatOff = 'FLAT_OFF';
    case PercentageOff = 'PERCENTAGE_OFF';
}