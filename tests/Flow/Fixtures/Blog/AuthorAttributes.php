<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Flow\Fixtures\Blog;

use Psr\Messages\Exception\UnsupportedDeserializationException;
use Serializer\SerializableInterface;

/**
 * The attributes of an author resource.
 *
 * @implements SerializableInterface<array{name: string}>
 */
final readonly class AuthorAttributes implements SerializableInterface
{
    public function __construct(
        public string $name,
    ) {
    }

    /**
     * @return array{name: string}
     */
    #[\Override]
    public function serialize(): array
    {
        return ['name' => $this->name];
    }

    /**
     * @throws UnsupportedDeserializationException always; output only
     */
    #[\Override]
    public static function deserialize(array $attributes): static
    {
        throw UnsupportedDeserializationException::for('Author attributes');
    }
}
