<?php

declare(strict_types=1);

namespace Psr\Messages\Link\Definition;

/**
 * A single JSON:API link: a type (the key under "links") and its target href.
 */
final readonly class Link
{
    public function __construct(
        public LinkTypeInterface $type,
        public Href $href,
    ) {
    }

    /**
     * Serializes a list of links into the JSON:API "links" object: a map of
     * link type to href.
     *
     * @param Link[] $links
     *
     * @return array<string, string>
     */
    public static function toArray(array $links): array
    {
        return array_combine(
            array_map(static fn (Link $link): string => (string) $link->type->value, $links),
            array_map(static fn (Link $link): string => $link->href->value, $links),
        );
    }
}
