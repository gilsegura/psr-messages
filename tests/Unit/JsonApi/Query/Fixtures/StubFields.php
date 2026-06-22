<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Unit\JsonApi\Query\Fixtures;

use Psr\Messages\JsonApi\Query\Definition\AbstractFields;

/**
 * A concrete sparse-fieldset value object: it reuses the fixed parse format and
 * resolves each type name to its typed resource type through the stub enum.
 */
final readonly class StubFields extends AbstractFields
{
    #[\Override]
    public static function deserialize(array $attributes): static
    {
        return new self(...self::parse($attributes, StubType::from(...)));
    }
}
