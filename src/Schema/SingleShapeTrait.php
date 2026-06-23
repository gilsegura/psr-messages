<?php

declare(strict_types=1);

namespace Psr\Messages\Schema;

/**
 * Default discrimination for a single-shape endpoint: when only one schema
 * applies to a request, supports() always matches. Schemas that distinguish
 * several shapes do not use this trait and implement supports() instead.
 *
 * The data is already validated against the endpoint's JSON Schema before this
 * runs, so there is nothing to check here — a single shape simply always applies.
 *
 * @phpstan-require-implements SchemaInterface
 */
trait SingleShapeTrait
{
    /**
     * @param array<array-key, mixed> $data
     */
    public static function supports(array $data): bool
    {
        return true;
    }
}
