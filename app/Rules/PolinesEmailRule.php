<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class PolinesEmailRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $fail('Format email tidak valid.');

            return;
        }

        $domain = strtolower((string) substr(strrchr($value, '@'), 1));

        if ($domain !== 'polines.ac.id' && ! str_ends_with($domain, '.polines.ac.id')) {
            $fail('Hanya email dengan domain *.polines.ac.id yang diizinkan.');
        }
    }
}
