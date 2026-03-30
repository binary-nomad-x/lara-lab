<?php

use Illuminate\Support\Str;

if (!function_exists('generateSku')) {
    function generateSku(int $charLength = 12, bool $isUpperCased = true): string
    {
        $randomString = Str::random($charLength);
        return $isUpperCased ? strtoupper($randomString) : $randomString;
    }
}
