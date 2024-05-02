<?php
namespace App\Services; 

class GeneratorService {
   

    /**
     *  Generating random alphanumeric character
     *  @param int $lenght
     * 
     *  @return string $random_alpha_numeric 
     */
    public static function randomAlphaNumeric($length=8) {
        $random_alpha_numeric ="";
        $key = array_merge(range('a', 'z'), range(0, 9), range('A', 'Z'));
        for ($i = 0; $i < $length; $i++) {
            $random_alpha_numeric .= $key[array_rand($key)];
        }
        return $random_alpha_numeric;
    }

     /**
     *  Generating random numeric character
     *  @param int $lenght
     * 
     *  @return string $random_numeric 
     */
    public static function randomNumber($length) {
        $key = array_merge(range(0, 9));
        for ($i = 0; $i < $length; $i++) {
            $random_number .= $key[array_rand($key)];
        }
        return (int) $random_number;
    }
}