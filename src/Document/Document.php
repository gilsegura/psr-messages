<?php

declare(strict_types=1);

namespace Psr\Messages\Document;

use Psr\Messages\Exception\UnsupportedDeserializationException;
use Serializer\SerializableInterface;

/**
 * A response document carrying a serializable payload, rendered to a media type
 * by its subclass. The plain-JSON form serializes the payload directly; the
 * JSON:API form wraps it as resource object(s). Output only.
 *
 * @implements SerializableInterface<array<string, mixed>>
 */
abstract readonly class Document implements SerializableInterface
{
    /**
     * @throws UnsupportedDeserializationException always; a response document is output only
     */
    #[\Override]
    public static function deserialize(array $attributes): static
    {
        throw UnsupportedDeserializationException::for('A response document');
    }

    /**
     * @return array<string, mixed>
     */
    #[\Override]
    abstract public function serialize(): array;
}
