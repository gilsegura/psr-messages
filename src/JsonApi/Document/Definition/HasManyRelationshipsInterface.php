<?php

declare(strict_types=1);

namespace Psr\Messages\JsonApi\Document\Definition;

/**
 * Implemented by resources that expose to-many relationships, keyed by name, and
 * can derive a new instance with one added.
 */
interface HasManyRelationshipsInterface
{
    /**
     * @return array<string, ToManyRelationship>
     */
    public function manyRelationships(): array;

    public function withManyRelationship(RelationshipNameInterface $name, ToManyRelationship $relationship): static;
}
