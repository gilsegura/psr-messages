<?php

declare(strict_types=1);

namespace Psr\Messages\JsonApi\Document\Definition;

/**
 * Implemented by anything identifiable as a JSON:API resource: it has a type
 * and an id. Shared by full resource objects and bare resource identifiers.
 */
interface HasResourceIdentifierInterface
{
    public ResourceTypeInterface $type { get; }

    public string $id { get; }
}
