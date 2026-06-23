<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\JsonApi\Document\Fixtures;

use Psr\Messages\JsonApi\Document\Definition\RelationshipNameInterface;

/**
 * A stub relationship-name enum, standing in for the relationship enum a
 * consuming library would define, used to type relationships and includes.
 */
enum StubRelationship: string implements RelationshipNameInterface
{
    case AUTHOR = 'author';
    case TAGS = 'tags';

    #[\Override]
    public function equals(RelationshipNameInterface $name): bool
    {
        return $this === $name;
    }
}
