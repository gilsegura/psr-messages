<?php

declare(strict_types=1);

namespace Psr\Messages\JsonApi\Document\Definition;

/**
 * Implemented by resources that expose to-many relationships, keyed by name.
 */
interface HasManyRelationshipsInterface
{
    /**
     * @return array<string, ToManyRelationship>
     */
    public function manyRelationships(): array;
}
