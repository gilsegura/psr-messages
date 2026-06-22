<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Unit\JsonApi\Document\Fixtures;

use Psr\Messages\JsonApi\Document\Definition\ResourceTypeInterface;

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
