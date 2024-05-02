<?php
namespace App\Traits\Enum; 

trait HasEnumStaticMethods {

    /**
     * Get all the values of the enums 
     * 
     * @return array <<int, string>>
     */
    public static function values(): array
    {
    
       return enum_exists(self::class) ?  array_column(self::cases(), 'value') : [];
    }

    /**
     * Get all the values of the enums 
     * 
     * @return array <<int, string>>
     */
    public static function valuesToUpperCase(): array
    {
       return array_map(function ($value){
            return strtoupper($value);
       }, self::values());    
    }


    /**
     * Get all name value pair of the enum 
     * 
     * @return array <<string, string>>
     */

    public static function forSelect(): array
    {
        return enum_exists(self::class) ? array_column(self::cases(), 'name', 'value') : [];
    }
}