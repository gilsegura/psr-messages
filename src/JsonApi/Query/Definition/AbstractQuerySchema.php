<?php

declare(strict_types=1);

namespace Psr\Messages\JsonApi\Query\Definition;

use Psr\Messages\Exception\UnsupportedSerializationException;
use Psr\Messages\Schema\SchemaInterface;

/**
 * Base for an endpoint's query schema. It carries the fixed machinery every
 * JSON:API query has — a query is input only, so serialize() always throws, and
 * by default a single query shape applies to a request, so supports() matches.
 *
 * Concrete query schemas live in the consuming library: each endpoint extends
 * this and, in deserialize(), composes only the query value objects it allows
 * (page, sort, includes, fields, filters), typing its own allowed includes,
 * sort fields and sparse fieldsets so they can be validated against the schema.
 * The fixed, shared pieces (this base and the value objects) stay here; the
 * per-resource concretions stay in the library.
 *
 * @template TAttributes of array
 *
 * @implements SchemaInterface<TAttributes>
 */
abstract readonly class AbstractQuerySchema implements SchemaInterface
{
    /**
     * One query shape applies per request, so by default every request matches.
     * Override when an endpoint distinguishes several query shapes.
     *
     * @param array<array-key, mixed> $data
     */
    #[\Override]
    public static function supports(array $data): bool
    {
        return true;
    }

    /**
     * @param array<array-key, mixed> $attributes
     */
    #[\Override]
    abstract public static function deserialize(array $attributes): static;

    /**
     * @return TAttributes
     *
     * @throws UnsupportedSerializationException always; a query schema is input only
     */
    #[\Override]
    public function serialize(): array
    {
        throw UnsupportedSerializationException::for('A query schema');
    }
}
