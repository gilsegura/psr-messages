<?php

declare(strict_types=1);

namespace Psr\Messages\JsonApi\Document\Definition;

use Psr\Messages\Exception\UnsupportedDeserializationException;
use Psr\Messages\JsonApi\Definition\HasMetaInterface;
use Psr\Messages\Link\Definition\HasLinksInterface;
use Psr\Messages\Link\Definition\Link;

/**
 * A to-one JSON:API relationship: its linkage is a single resource identifier,
 * or null when the relationship is empty. The constructor takes the identifier;
 * links and meta are added through immutable withXxx() methods.
 */
final readonly class ToOneRelationship implements RelationshipInterface, HasLinksInterface, HasMetaInterface
{
    /**
     * @param Link[]               $links
     * @param array<string, mixed> $meta
     */
    public function __construct(
        public ?ResourceIdentifier $identifier = null,
        public array $links = [],
        public array $meta = [],
    ) {
    }

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

    #[\Override]
    public function withLinks(Link ...$links): static
    {
        return new self($this->identifier, $links, $this->meta);
    }

    #[\Override]
    public function withMeta(array $meta): static
    {
        return new self($this->identifier, $this->links, $meta);
    }
}
