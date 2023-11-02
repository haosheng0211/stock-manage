<?php

if (! function_exists('force_format_number')) {
    function force_format_number(mixed $value): int
    {
        if (is_int($value)) {
            return $value;
        }

        return (int) preg_replace('/[^0-9.]/', '', $value);
    }
}
