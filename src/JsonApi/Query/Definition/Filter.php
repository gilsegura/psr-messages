<?php

declare(strict_types=1);

namespace Psr\Messages\JsonApi\Query\Definition;

/**
 * A single JSON:API filter, e.g. filter[status]=active becomes field "status",
 * value "active".
 */
final readonly class Filter
{
    public function __construct(
        public string $field,
        public string $value,
    ) {
    }
}
