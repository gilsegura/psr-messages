<?php

declare(strict_types=1);

namespace Psr\Messages\JsonApi\Query\Definition;

use Psr\Messages\Support\PathNavigator;

/**
 * A single JSON:API include path, e.g. "author" or the nested "comments.author"
 * (the authors of a resource's comments). The dot-separated structure is
 * navigated through PathNavigator: segments() splits it, head() is the first
 * relationship to resolve, tail() the remaining path to resolve from there, and
 * isNested() tells a nested path from a plain one.
 */
final readonly class Path
{
    public function __construct(
        public string $value,
    ) {
    }

    /**
     * The path split into its relationship segments, e.g. "comments.author"
     * becomes ["comments", "author"].
     *
     * @return string[]
     */
    public function segments(): array
    {
        return PathNavigator::segments($this->value);
    }

    /**
     * The first relationship in the path, e.g. "comments" for "comments.author".
     */
    public function head(): string
    {
        return PathNavigator::head($this->value);
    }

    /**
     * The relationships after the first, e.g. ["author"] for "comments.author".
     *
     * @return string[]
     */
    public function tail(): array
    {
        return PathNavigator::tail($this->value);
    }

    /**
     * Whether the path traverses more than one relationship.
     */
    public function isNested(): bool
    {
        return PathNavigator::isNested($this->value);
    }
}
