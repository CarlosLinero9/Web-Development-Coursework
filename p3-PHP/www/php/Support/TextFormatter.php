<?php

declare(strict_types=1);

namespace App\Support;

final class TextFormatter
{
    public static function uppercaseLocations(string $text, array $locations): string
    {
        $formatted = $text;

        foreach ($locations as $location) {
            $escaped = preg_quote($location, '/');
            $formatted = preg_replace_callback(
                '/\\b' . $escaped . '\\b/iu',
                static fn (): string => mb_strtoupper($location, 'UTF-8'),
                $formatted
            ) ?? $formatted;
        }

        return $formatted;
    }

    public static function formatDescription(string $text): array
    {
        $paragraphs = preg_split('/\R{2,}/u', trim($text)) ?: [];

        return array_values(array_filter(array_map('trim', $paragraphs)));
    }
}
