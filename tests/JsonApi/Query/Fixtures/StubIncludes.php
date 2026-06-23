<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\JsonApi\Query\Fixtures;

use Psr\Messages\JsonApi\Query\Definition\AbstractIncludes;

/**
 * A concrete includes value object, as a consuming library would define one per
 * resource: it reuses the fixed JSON:API parse format and only types the result.
 */
final readonly class StubIncludes extends AbstractIncludes
{
    #[\Override]
    public static function deserialize(array $attributes): static
    {
        return new self(...self::parse($attributes));
    }
}
