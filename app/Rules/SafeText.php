<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class SafeText implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (preg_match('/^[a-zA-Z0-9\s\.\p{L}&\-,\']+$/u', $value) !== 1) {
            $fail("The :attribute field may only contain letters, numbers, spaces, and common punctuation (. & - , ').");
        }
    }
}
