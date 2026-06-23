<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Json\Fixtures;

use Psr\Messages\Exception\UnsupportedDeserializationException;
use Serializer\SerializableInterface;

/**
 * A serializable read model for the plain-JSON flow.
 *
 * @implements SerializableInterface<array<string, mixed>>
 */
final readonly class StubReadModel implements SerializableInterface
{
    public function __construct(
        public string $id,
        public string $name,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    #[\Override]
    public function serialize(): array
    {
        return ['id' => $this->id, 'name' => $this->name];
    }

    /**
     * @throws UnsupportedDeserializationException always; a read model is output only here
     */
    #[\Override]
    public static function deserialize(array $attributes): static
    {
        throw UnsupportedDeserializationException::for('A stub read model');
    }
}
