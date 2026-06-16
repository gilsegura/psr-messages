<?php

declare(strict_types=1);

namespace Psr\Messages\JsonApi\Query\Definition;

/**
 * A single JSON:API include path, e.g. "author" or "comments.author".
 */
final readonly class Path
{
    public function __construct(
        public string $value,
    ) {
    }
}
