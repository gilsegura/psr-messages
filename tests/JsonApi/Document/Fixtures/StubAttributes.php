<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\JsonApi\Document\Fixtures;

use Psr\Messages\Exception\UnsupportedDeserializationException;
use Serializer\SerializableInterface;

/**
 * @implements SerializableInterface<array<string, mixed>>
 */
final readonly class StubAttributes implements SerializableInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(private array $data)
    {
    }

    /**
     * @return array<string, mixed>
     */
    #[\Override]
    public function serialize(): array
    {
        return $this->data;
    }

    #[\Override]
    public static function deserialize(array $attributes): static
    {
        throw UnsupportedDeserializationException::for('Stub attributes');
    }
}
