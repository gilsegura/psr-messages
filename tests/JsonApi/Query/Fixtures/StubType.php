<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\JsonApi\Query\Fixtures;

use Psr\Messages\JsonApi\Document\Definition\ResourceTypeInterface;

/**
 * A stub resource type enum, standing in for the type enum a consuming library
 * would define, used to type sparse fieldsets in the query fixtures.
 */
enum StubType: string implements ResourceTypeInterface
{
    case ARTICLE = 'articles';
    case PERSON = 'people';

    #[\Override]
    public function equals(ResourceTypeInterface $type): bool
    {
        return $this === $type;
    }
}
