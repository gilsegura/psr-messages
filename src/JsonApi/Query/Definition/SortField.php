<?php

declare(strict_types=1);

namespace Psr\Messages\JsonApi\Query\Definition;

final readonly class SortField
{
    public function __construct(
        public string $field,
        public SortDirection $direction,
    ) {
    }

    public static function asc(string $field): self
    {
        return new self($field, SortDirection::ASC);
    }

    public static function desc(string $field): self
    {
        return new self($field, SortDirection::DESC);
    }
}
