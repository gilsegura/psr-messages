<?php

declare(strict_types=1);

namespace Psr\Messages\JsonApi\Document\Definition;

use Psr\Messages\Exception\UnsupportedDeserializationException;
use Psr\Messages\JsonApi\Definition\HasMetaInterface;
use Psr\Messages\Link\Definition\HasLinksInterface;
use Psr\Messages\Link\Definition\Link;
use Serializer\SerializableInterface;

/**
 * A concrete JSON:API resource object carrying type, id and attributes, and
 * optionally relationships, links and meta. The constructor takes only the
 * minimal identity ({type, id, attributes}); relationships, links and meta are
 * added through immutable withXxx() methods, so a resource is built up one
 * concern at a time. Build it directly, or wrap it to expose domain data.
 *
 * State is exposed as readonly properties (the interface contracts declare them
 * as `{ get; }`); withXxx() derive new instances.
 */
final readonly class ResourceObject implements ResourceInterface, HasLinksInterface, HasMetaInterface
{
    /** @var array<string, ToOneRelationship> */
    public array $oneRelationships;

    /** @var array<string, ToManyRelationship> */
    public array $manyRelationships;

    /** @var Link[] */
    public array $links;

    /** @var array<string, mixed> */
    public array $meta;

    /**
     * @param SerializableInterface<array<string, mixed>> $attributes
     * @param array<string, ToOneRelationship>            $oneRelationships
     * @param array<string, ToManyRelationship>           $manyRelationships
     * @param Link[]                                      $links
     * @param array<string, mixed>                        $meta
     */
    public function __construct(
        public ResourceTypeInterface $type,
        public string $id,
        public SerializableInterface $attributes,
        array $oneRelationships = [],
        array $manyRelationships = [],
        array $links = [],
        array $meta = [],
    ) {
        $this->oneRelationships = $oneRelationships;
        $this->manyRelationships = $manyRelationships;
        $this->links = $links;
        $this->meta = $meta;
    }

    #[\Override]
    public function serialize(): array
    {
        $object = [
            'type' => $this->type->value,
            'id' => $this->id,
            'attributes' => $this->attributes->serialize(),
        ];

        $relationships = $this->oneRelationships + $this->manyRelationships;

        if ([] !== $relationships) {
            $object['relationships'] = array_map(
                static fn (RelationshipInterface $relationship): array => $relationship->serialize(),
                $relationships,
            );
        }

        if ([] !== $this->links) {
            $object['links'] = Link::toArray($this->links);
        }

        if ([] !== $this->meta) {
            $object['meta'] = $this->meta;
        }

        return $object;
    }

    /**
     * @throws UnsupportedDeserializationException always; a resource object is output only
     */
    #[\Override]
    public static function deserialize(array $attributes): static
    {
        throw UnsupportedDeserializationException::for('A resource object');
    }

    /**
     * Adds a to-one relationship under the given name, returning a new resource.
     */
    public function withOneRelationship(RelationshipNameInterface $name, ToOneRelationship $relationship): static
    {
        return new self(
            $this->type,
            $this->id,
            $this->attributes,
            [(string) $name->value => $relationship] + $this->oneRelationships,
            $this->manyRelationships,
            $this->links,
            $this->meta,
        );
    }

    /**
     * Adds a to-many relationship under the given name, returning a new resource.
     */
    public function withManyRelationship(RelationshipNameInterface $name, ToManyRelationship $relationship): static
    {
        return new self(
            $this->type,
            $this->id,
            $this->attributes,
            $this->oneRelationships,
            [(string) $name->value => $relationship] + $this->manyRelationships,
            $this->links,
            $this->meta,
        );
    }

    #[\Override]
    public function withLinks(Link ...$links): static
    {
        return new self($this->type, $this->id, $this->attributes, $this->oneRelationships, $this->manyRelationships, $links, $this->meta);
    }

    #[\Override]
    public function withMeta(array $meta): static
    {
        return new self($this->type, $this->id, $this->attributes, $this->oneRelationships, $this->manyRelationships, $this->links, $meta);
    }
}
