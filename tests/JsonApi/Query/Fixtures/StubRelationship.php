<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\JsonApi\Query\Fixtures;

use Psr\Messages\JsonApi\Document\Definition\RelationshipNameInterface;

/**
 * A stub relationship-name enum used to type the include lookups in the query
 * fixtures and tests.
 */
enum StubRelationship: string implements RelationshipNameInterface
{
    case AUTHOR = 'author';
    case COMMENTS = 'comments';

    #[\Override]
    public function equals(RelationshipNameInterface $name): bool
    {
        return $this === $name;
    }
}
