<?php

declare(strict_types=1);

namespace Psr\Messages\JsonApi\Include;

use Psr\Messages\JsonApi\Document\Definition\RelationshipNameInterface;
use Psr\Messages\JsonApi\Document\Definition\ResourceIdentifier;
use Psr\Messages\JsonApi\Document\Definition\ResourceInterface;

/**
 * The outcome of resolving one include for a set of primary models: the embedded
 * resources for the document's "included" section, and the relationship linkage
 * keyed by primary-model id. Produced in a single load so the builder reuses it
 * for both the relationships and the included section without querying twice.
 *
 * @template TPrimary of object
 */
final readonly class ResolvedInclude
{
    /**
     * @param ResourceInterface[]                 $resources the embedded resources
     * @param array<string, ResourceIdentifier[]> $linkage   identifiers per primary-model id
     */
    public function __construct(
        public RelationshipNameInterface $name,
        public array $resources,
        public array $linkage,
    ) {
    }
}
