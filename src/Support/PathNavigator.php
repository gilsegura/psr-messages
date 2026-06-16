<?php

declare(strict_types=1);

namespace Psr\Messages\Support;

/**
 * Navigation over a delimited path string, e.g. a JSON:API include path
 * ("comments.author", dot-separated) or a structural path ("data.attributes").
 * The separator is configurable so the same logic serves different path
 * flavours. Empty segments (from leading, trailing, or repeated separators)
 * are dropped.
 */
final class PathNavigator
{
    /**
     * @param non-empty-string $separator
     *
     * @return string[] the path split into its non-empty segments
     */
    public static function segments(string $path, string $separator = '.'): array
    {
        return array_values(array_filter(
            array_map(trim(...), explode($separator, $path)),
            static fn (string $segment): bool => '' !== $segment,
        ));
    }

    /**
     * The first segment, or an empty string if the path has none.
     *
     * @param non-empty-string $separator
     */
    public static function head(string $path, string $separator = '.'): string
    {
        return self::segments($path, $separator)[0] ?? '';
    }

    /**
     * Every segment after the first.
     *
     * @param non-empty-string $separator
     *
     * @return string[]
     */
    public static function tail(string $path, string $separator = '.'): array
    {
        return \array_slice(self::segments($path, $separator), 1);
    }

    /**
     * Number of non-empty segments.
     *
     * @param non-empty-string $separator
     */
    public static function depth(string $path, string $separator = '.'): int
    {
        return \count(self::segments($path, $separator));
    }

    /**
     * Whether the path has more than one segment (e.g. "comments.author").
     *
     * @param non-empty-string $separator
     */
    public static function isNested(string $path, string $separator = '.'): bool
    {
        return self::depth($path, $separator) > 1;
    }
}
