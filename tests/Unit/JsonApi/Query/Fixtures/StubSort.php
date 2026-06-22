<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Unit\JsonApi\Query\Fixtures;

use Psr\Messages\JsonApi\Query\Definition\AbstractSort;

/**
 * A concrete sort value object: it reuses the fixed JSON:API sort format and
 * only types the result.
 */
final readonly class StubSort extends AbstractSort
{
    #[\Override]
    public static function deserialize(array $attributes): static
    {
        return new self(...self::parse($attributes));
    }
}
