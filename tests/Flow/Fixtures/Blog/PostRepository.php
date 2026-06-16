<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Flow\Fixtures\Blog;

use Psr\Messages\Support\Optional;

/**
 * An in-memory blog post store for the flow tests. Stands in for whatever the
 * application uses to persist and query posts.
 */
final class PostRepository
{
    /** @var array<string, Author> */
    private array $authors;

    /** @var Post[] */
    private array $posts;

    private int $sequence = 0;

    public function __construct()
    {
        $ada = new Author('a-1', 'Ada Lovelace');
        $alan = new Author('a-2', 'Alan Turing');

        $this->authors = ['a-1' => $ada, 'a-2' => $alan];

        $this->posts = [
            new Post('p-1', 'First', 'Body one', $ada, [
                ['id' => 'c-1', 'body' => 'Nice post', 'authorId' => 'a-2'],
            ]),
            new Post('p-2', 'Second', 'Body two', $alan),
            new Post('p-3', 'Third', 'Body three', $ada),
        ];
    }

    public function create(string $title, string $body, string $authorId): Post
    {
        $author = $this->authors[$authorId] ?? new Author($authorId, 'Unknown');

        ++$this->sequence;
        $post = new Post('p-new-'.$this->sequence, $title, $body, $author);

        $this->posts[] = $post;

        return $post;
    }

    public function find(string $id): ?Post
    {
        return array_find($this->posts, static fn (Post $post): bool => $post->id === $id);
    }

    /**
     * Applies a partial update: only the present fields change. Returns the new
     * Post (the aggregate is readonly, so updating means replacing).
     *
     * @param Optional<string> $title
     * @param Optional<string> $body
     * @param Optional<string> $authorId
     */
    public function update(string $id, Optional $title, Optional $body, Optional $authorId): Post
    {
        $current = $this->find($id);

        if (!$current instanceof Post) {
            throw new \RuntimeException(\sprintf('Post %s not found.', $id));
        }

        $author = $current->author;

        if ($authorId->isPresent()) {
            $author = $this->authors[$authorId->get()] ?? new Author($authorId->get(), 'Unknown');
        }

        $updated = new Post(
            $current->id,
            $title->orElse($current->title),
            $body->orElse($current->body),
            $author,
            $current->comments,
        );

        $this->posts = array_map(
            static fn (Post $post): Post => $post->id === $id ? $updated : $post,
            $this->posts,
        );

        return $updated;
    }

    /**
     * @return Post[]
     */
    public function page(int $number, int $size): array
    {
        return \array_slice($this->posts, ($number - 1) * $size, $size);
    }

    public function total(): int
    {
        return \count($this->posts);
    }
}
