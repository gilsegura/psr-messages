<?php

declare(strict_types=1);

namespace Psr\Messages\JsonApi\Document\Definition;

/**
 * Implemented by resources that expose to-one relationships, keyed by name.
 */
interface HasOneRelationshipInterface
{
    /**
     * @return array<string, ToOneRelationship>
     */
    public function oneRelationships(): array;
}
