<?php

declare(strict_types=1);

namespace Psr\Messages\Support;

/**
 * Helpers to derive human-readable text from class names.
 */
final class ClassName
{
    /**
     * The short class name (without namespace) of a fully-qualified class name.
     */
    public static function short(string $fqcn): string
    {
        $parts = explode('\\', $fqcn);

        return end($parts);
    }

    /**
     * A human title from a class name: drops a trailing "Exception" and splits
     * camel case, e.g. "MalformedContentException" becomes "Malformed content".
     */
    public static function toTitle(string $fqcn, string $dropSuffix = 'Exception'): string
    {
        $shortName = self::short($fqcn);

        if (
            '' !== $dropSuffix
            && str_ends_with($shortName, $dropSuffix)
        ) {
            $shortName = substr($shortName, 0, -\strlen($dropSuffix));
        }

        $spaced = (string) preg_replace('/(?<!^)[A-Z]/', ' $0', $shortName);

        return mb_ucfirst(mb_strtolower($spaced));
    }
}
