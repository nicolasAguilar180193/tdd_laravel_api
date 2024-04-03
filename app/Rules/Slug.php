<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Slug implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (preg_match('/_/', $value)) 
            $fail(__('validation.no_underscores', ['attribute' => 'data.attributes.slug']));

        if (preg_match('/^-/', $value)) 
            $fail(__('validation.no_starting_dashes', ['attribute' => 'data.attributes.slug']));

        if (preg_match('/-$/', $value)) 
            $fail(__('validation.no_ending_dashes', ['attribute' => 'data.attributes.slug']));    
    }
}
