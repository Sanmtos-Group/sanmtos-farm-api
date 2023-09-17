<?php 

/**
 * Flatten multidimensional array to single array
 * 
 * @param array $array
 * @return array $result;
 */
function flattenArray($array) : array
{
    $result = [];

    foreach ($array as $element) {
        if (is_array($element)) {
            $result = array_merge($result, flattenArray($element));
        } else {
            $result[] = $element;
        }
    }

    return $result;
}