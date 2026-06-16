<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Flow\Fixtures\Blog;

/**
 * A minimal in-memory author for the flow tests.
 */
final readonly class Author
{
    public function __construct(
        public string $id,
        public string $name,
    ) {
    }
}
