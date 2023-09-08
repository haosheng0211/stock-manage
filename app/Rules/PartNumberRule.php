<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PartNumberRule implements Rule
{
    public function passes($attribute, $value): bool
    {
        return preg_match('/^[^&?@~;^"{}!$]*$/u', $value);
    }

    public function message(): string
    {
        return trans('validation.regex');
    }
}
