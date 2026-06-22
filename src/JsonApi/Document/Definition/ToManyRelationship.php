<?php

declare(strict_types=1);

namespace Psr\Messages\JsonApi\Document\Definition;

use Psr\Messages\Exception\UnsupportedDeserializationException;
use Psr\Messages\JsonApi\Definition\HasMetaInterface;
use Psr\Messages\Link\Definition\HasLinksInterface;
use Psr\Messages\Link\Definition\Link;

/**
 * A to-many JSON:API relationship: its linkage is a list of resource
 * identifiers (possibly empty). The constructor takes only the identifiers;
 * links and meta are added through immutable withXxx() methods, and identifiers
 * can be appended one at a time with withIdentifier().
 */
final readonly class ToManyRelationship implements RelationshipInterface, HasLinksInterface, HasMetaInterface
{
    /** @var ResourceIdentifier[] */
    private array $identifiers;

    /** @var Link[] */
    private array $links;

    /** @var array<string, mixed> */
    private array $meta;

    /**
     * @param ResourceIdentifier[] $identifiers
     * @param Link[]               $links
     * @param array<string, mixed> $meta
     */
    public function __construct(array $identifiers = [], array $links = [], array $meta = [])
    {
        $this->identifiers = $identifiers;
        $this->links = $links;
        $this->meta = $meta;
    }

    /**
     * @return array<string, mixed>
     */
    #[\Override]
    public function serialize(): array
    {
        $relationship = ['data' => array_map(
            static fn (ResourceIdentifier $identifier): array => $identifier->serialize(),
            $this->identifiers,
        )];

        if ([] !== $this->links) {
            $relationship['links'] = Link::toArray($this->links);
        }

        if ([] !== $this->meta) {
            $relationship['meta'] = $this->meta;
        }

        return $relationship;
    }

    /**
     * @throws UnsupportedDeserializationException always; a relationship is output only
     */
    #[\Override]
    public static function deserialize(array $attributes): static
    {
        throw UnsupportedDeserializationException::for('A to-many relationship');
    }

    /**
     * @return ResourceIdentifier[]
     */
    public function identifiers(): array
    {
        return $this->identifiers;
    }

    /**
     * Appends a resource identifier to the linkage, returning a new relationship.
     */
    public function withIdentifier(ResourceIdentifier $identifier): static
    {
        return new self([...$this->identifiers, $identifier], $this->links, $this->meta);
    }

    /**
     * @return Link[]
     */
    #[\Override]
    public function links(): array
    {
        return $this->links;
    }

    #[\Override]
    public function withLinks(Link ...$links): static
    {
        return new self($this->identifiers, $links, $this->meta);
    }

    /**
     * @return array<string, mixed>
     */
    #[\Override]
    public function meta(): array
    {
        return $this->meta;
    }

    /**
     * @param array<string, mixed> $meta
     */
    #[\Override]
    public function withMeta(array $meta): static
    {
        return new self($this->identifiers, $this->links, $meta);
    }
}
