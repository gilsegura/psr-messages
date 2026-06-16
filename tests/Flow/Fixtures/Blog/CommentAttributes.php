<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Flow\Fixtures\Blog;

use Psr\Messages\Exception\UnsupportedDeserializationException;
use Serializer\SerializableInterface;

/**
 * The attributes of a comment resource.
 *
 * @implements SerializableInterface<array{body: string}>
 */
final readonly class CommentAttributes implements SerializableInterface
{
    public function __construct(
        public string $body,
    ) {
    }

    /**
     * @return array{body: string}
     */
    #[\Override]
    public function serialize(): array
    {
        return ['body' => $this->body];
    }

    /**
     * @throws UnsupportedDeserializationException always; output only
     */
    #[\Override]
    public static function deserialize(array $attributes): static
    {
        throw UnsupportedDeserializationException::for('Comment attributes');
    }
}
