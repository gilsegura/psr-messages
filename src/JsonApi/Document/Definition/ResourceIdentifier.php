<?php

declare(strict_types=1);

namespace Psr\Messages\JsonApi\Document\Definition;

use Psr\Messages\Exception\UnsupportedDeserializationException;
use Psr\Messages\JsonApi\Definition\HasMetaInterface;
use Serializer\SerializableInterface;

/**
 * A JSON:API resource identifier: the minimal {type, id} reference to a
 * resource, used as relationship linkage. May carry optional meta.
 *
 * @implements SerializableInterface<array<string, mixed>>
 */
final readonly class ResourceIdentifier implements HasResourceIdentifierInterface, HasMetaInterface, SerializableInterface
{
    /** @var array<string, mixed> */
    public array $meta;

    /**
     * @param array<string, mixed> $meta
     */
    public function __construct(
        public ResourceTypeInterface $type,
        public string $id,
        array $meta = [],
    ) {
        $this->meta = $meta;
    }

    #[\Override]
    public function serialize(): array
    {
        $identifier = ['type' => $this->type->value, 'id' => $this->id];

        if ([] !== $this->meta) {
            $identifier['meta'] = $this->meta;
        }

        return $identifier;
    }

    /**
     * @throws UnsupportedDeserializationException always; a resource identifier is output only
     */
    #[\Override]
    public static function deserialize(array $attributes): static
    {
        throw UnsupportedDeserializationException::for('A resource identifier');
    }

    #[\Override]
    public function withMeta(array $meta): static
    {
        return new self($this->type, $this->id, $meta);
    }
}
