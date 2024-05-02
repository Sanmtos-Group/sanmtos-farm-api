<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MaxWords implements ValidationRule
{
     /**
     * @var int $word_count 
     */
    private int $word_count;

    public function __construct(int $word_count)
    {
        $this->word_count = $word_count;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        
        if (str_word_count($value) > $this->word_count) {
            $fail('The :attribute must not be greater than '.$this->word_count.' words.');
        }
    }
}
