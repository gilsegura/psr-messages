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
     * The "self" link: the resource or document's own location.
     */
    public static function self(Href $href): self
    {
        return new self(LinkType::SELF, $href);
    }

    /**
     * The "first" pagination link.
     */
    public static function first(Href $href): self
    {
        return new self(LinkType::FIRST, $href);
    }

    /**
     * The "last" pagination link.
     */
    public static function last(Href $href): self
    {
        return new self(LinkType::LAST, $href);
    }

    /**
     * The "prev" pagination link.
     */
    public static function prev(Href $href): self
    {
        return new self(LinkType::PREV, $href);
    }

    /**
     * The "next" pagination link.
     */
    public static function next(Href $href): self
    {
        return new self(LinkType::NEXT, $href);
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
        $map = [];

        foreach ($links as $link) {
            $map[(string) $link->type->value] = $link->href->value;
        }

        return $map;
    }
}
