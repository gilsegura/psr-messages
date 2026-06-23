<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\JsonApi\Document\Fixtures;

use Psr\Messages\JsonApi\Query\Definition\AbstractFields;

/**
 * A concrete sparse-fieldset value object for the presenter test, resolving each
 * type name to its typed resource type through the stub enum.
 */
final readonly class StubFields extends AbstractFields
{
    #[\Override]
    public static function deserialize(array $attributes): static
    {
        return new self(...self::parse($attributes, StubType::from(...)));
    }
}
