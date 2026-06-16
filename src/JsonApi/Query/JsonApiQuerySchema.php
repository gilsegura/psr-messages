<?php

declare(strict_types=1);

namespace Psr\Messages\JsonApi\Query;

use Psr\Messages\Exception\UnsupportedSerializationException;
use Psr\Messages\JsonApi\Query\Definition\Fields;
use Psr\Messages\JsonApi\Query\Definition\Filters;
use Psr\Messages\JsonApi\Query\Definition\Includes;
use Psr\Messages\JsonApi\Query\Definition\Page;
use Psr\Messages\JsonApi\Query\Definition\Sort;
use Psr\Messages\Schema\SchemaInterface;

/**
 * Default JSON:API query schema: builds the standard JSON:API query value
 * objects (page, sort, filters, includes, fields) from the validated query parameters.
 *
 * One query schema per request, so supports() always matches. Define your own
 * SchemaInterface for endpoints with a different query shape.
 *
 * @implements SchemaInterface<array<string, mixed>>
 */
final readonly class JsonApiQuerySchema implements SchemaInterface
{
    public function __construct(
        public Page $page,
        public Sort $sort,
        public Filters $filters,
        public Includes $includes,
        public Fields $fields,
    ) {
    }

    #[\Override]
    public static function supports(array $data): bool
    {
        return true;
    }

    /**
     * @param array<string, mixed> $attributes
     */
    #[\Override]
    public static function deserialize(array $attributes): static
    {
        return new self(
            Page::deserialize($attributes),
            Sort::deserialize($attributes),
            Filters::deserialize($attributes),
            Includes::deserialize($attributes),
            Fields::deserialize($attributes),
        );
    }

    /**
     * @throws UnsupportedSerializationException always; query value objects are input only
     */
    #[\Override]
    public function serialize(): array
    {
        throw UnsupportedSerializationException::for('A JSON:API query schema');
    }
}
