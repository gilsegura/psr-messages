<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Flow\Fixtures\Blog;

/**
 * A minimal in-memory blog post aggregate for the flow tests: a post written by
 * an author, with comments. Stands in for whatever the application persists.
 */
final readonly class Post
{
    /**
     * @param array{id: string, body: string, authorId: string}[] $comments
     */
    public function __construct(
        public string $id,
        public string $title,
        public string $body,
        public Author $author,
        public array $comments = [],
    ) {
    }
}
