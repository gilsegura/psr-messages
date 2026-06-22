<?php

declare(strict_types=1);

namespace Psr\Messages\JsonApi\Query\Definition;

use Psr\Messages\Exception\UnsupportedSerializationException;
use Serializer\SerializableInterface;

/**
 * Base for JSON:API filtering: filter[field]=value pairs, e.g.
 * "filter[status]=active". This base owns the fixed JSON:API format (extracting
 * the field/value pairs) and the queries over them; the concrete subclass in the
 * consuming library types the resource's filters. Which filters are permitted is
 * enforced by the endpoint's JSON Schema before parsing.
 *
 * @implements SerializableInterface<array{filter?: array<string, string>}>
 */
abstract readonly class AbstractFilters implements SerializableInterface
{
    /** @var Filter[] */
    public array $filters;

    final public function __construct(Filter ...$filters)
    {
        $this->filters = $filters;
    }

    /**
     * The value requested for a filter field, or null when it was not provided.
     */
    final public function forField(string $field): ?string
    {
        $filter = array_find(
            $this->filters,
            static fn (Filter $candidate): bool => $candidate->field === $field,
        );

        return $filter?->value;
    }

    /**
     * Whether any filter was requested.
     */
    final public function isEmpty(): bool
    {
        return [] === $this->filters;
    }

    /**
     * Parses the standard JSON:API "filter[field]=value" parameter into filters.
     * The format is fixed here; the subclass decides how to type the result.
     *
     * @param array<array-key, mixed> $attributes
     *
     * @return Filter[]
     */
    final protected static function parse(array $attributes): array
    {
        if (!isset($attributes['filter']) || !\is_array($attributes['filter'])) {
            return [];
        }

        /** @var array<string, string> $filter */
        $filter = $attributes['filter'];

        return array_map(
            static fn (string $field): Filter => new Filter($field, $filter[$field]),
            array_keys($filter),
        );
    }

    /**
     * @param array<array-key, mixed> $attributes
     */
    #[\Override]
    abstract public static function deserialize(array $attributes): static;

    /**
     * @return array{filter?: array<string, string>}
     *
     * @throws UnsupportedSerializationException always; query value objects are input only
     */
    #[\Override]
    final public function serialize(): array
    {
        throw UnsupportedSerializationException::for('A filters value object');
    }
}
