<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Unit\Middleware\Fixtures;

use Psr\Messages\JsonApi\Query\Definition\AbstractQuerySchema;
use Psr\Messages\JsonApi\Query\Definition\Page;

/**
 * A concrete query schema for the middleware test: it composes only Page, as a
 * minimal endpoint query would, reusing the fixed query-schema machinery.
 *
 * @extends AbstractQuerySchema<array{page?: array{number?: string, size?: string}}>
 */
final readonly class StubQuerySchema extends AbstractQuerySchema
{
    public function __construct(
        public Page $page,
    ) {
    }

    #[\Override]
    public static function deserialize(array $attributes): static
    {
        return new self(Page::deserialize($attributes));
    }
}
