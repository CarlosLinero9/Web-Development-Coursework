<?php

declare(strict_types=1);

namespace App\Support;

final class Input
{
    public static function cleanText(string $value): string
    {
        $value = trim($value);
        $value = strip_tags($value);
        $value = preg_replace('/\s+/u', ' ', $value) ?? $value;

        return trim($value);
    }

    public static function cleanEmail(string $value): string
    {
        return strtolower(trim($value));
    }

    public static function validateNewsId(?string $value): ?int
    {
        if ($value === null) {
            return null;
        }

        $filtered = filter_var($value, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1],
        ]);

        return $filtered === false ? null : (int) $filtered;
    }
}
