<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\JsonApi\Query\Fixtures;

use Psr\Messages\JsonApi\Query\Definition\AbstractFilters;

/**
 * A concrete filters value object: it reuses the fixed JSON:API filter format
 * and only types the result.
 */
final readonly class StubFilters extends AbstractFilters
{
    #[\Override]
    public static function deserialize(array $attributes): static
    {
        return new self(...self::parse($attributes));
    }
}
