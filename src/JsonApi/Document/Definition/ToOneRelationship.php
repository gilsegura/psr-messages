<?php

declare(strict_types=1);

namespace Psr\Messages\JsonApi\Document\Definition;

use Psr\Messages\Exception\UnsupportedDeserializationException;
use Psr\Messages\JsonApi\Definition\HasMetaInterface;
use Psr\Messages\Link\Definition\HasLinksInterface;
use Psr\Messages\Link\Definition\Link;

/**
 * A to-one JSON:API relationship: its linkage is a single resource identifier,
 * or null when the relationship is empty. The constructor takes only the
 * identifier; links and meta are added through immutable withXxx() methods, and
 * the linkage can be set with withIdentifier().
 */
final readonly class ToOneRelationship implements RelationshipInterface, HasLinksInterface, HasMetaInterface
{
    /** @var Link[] */
    private array $links;

    /** @var array<string, mixed> */
    private array $meta;

    /**
     * @param Link[]               $links
     * @param array<string, mixed> $meta
     */
    public function __construct(
        private ?ResourceIdentifier $identifier = null,
        array $links = [],
        array $meta = [],
    ) {
        $this->links = $links;
        $this->meta = $meta;
    }

    /**
     * @return array<string, mixed>
     */
    #[\Override]
    public function serialize(): array
    {
        $relationship = ['data' => $this->identifier?->serialize()];

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
        throw UnsupportedDeserializationException::for('A to-one relationship');
    }

    public function identifier(): ?ResourceIdentifier
    {
        return $this->identifier;
    }

    /**
     * Sets the linkage identifier, returning a new relationship.
     */
    public function withIdentifier(ResourceIdentifier $identifier): static
    {
        return new self($identifier, $this->links, $this->meta);
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
        return new self($this->identifier, $links, $this->meta);
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
        return new self($this->identifier, $this->links, $meta);
    }
}
