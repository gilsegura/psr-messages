<?php

declare(strict_types=1);

namespace Psr\Messages\JsonApi\Document\Definition;

/**
 * Implemented by resources that expose to-one relationships, keyed by name, and
 * can derive a new instance with one added.
 */
interface HasOneRelationshipInterface
{
    /**
     * @return array<string, ToOneRelationship>
     */
    public function oneRelationships(): array;

    public function withOneRelationship(RelationshipNameInterface $name, ToOneRelationship $relationship): static;
}
