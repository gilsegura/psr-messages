<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Flow\Fixtures;

use Psr\Messages\JsonApi\Document\Definition\ResourceTypeInterface;

/**
 * The resource types of the example blog domain used in the flow tests.
 */
enum ResourceType: string implements ResourceTypeInterface
{
    case POST = 'posts';
    case COMMENT = 'comments';
    case AUTHOR = 'authors';
}
