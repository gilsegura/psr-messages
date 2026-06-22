<?php

declare(strict_types=1);

namespace Psr\Messages\JsonApi\Query\Definition;

use Psr\Messages\Exception\UnsupportedSerializationException;
use Psr\Messages\Support\PathNavigator;
use Serializer\SerializableInterface;

/**
 * Base for JSON:API sorting: a comma-separated list of fields, each optionally
 * prefixed with "-" for descending order, e.g. "sort=-created,name". This base
 * owns the fixed JSON:API format (splitting and the leading "-" convention); the
 * concrete subclass in the consuming library types the resource's sortable
 * fields. Which fields are sortable is enforced by the endpoint's JSON Schema
 * before parsing.
 *
 * @implements SerializableInterface<array{sort?: string}>
 */
abstract readonly class AbstractSort implements SerializableInterface
{
    /** @var SortField[] */
    public array $fields;

    final public function __construct(SortField ...$fields)
    {
        $this->fields = $fields;
    }

    /**
     * Whether any sort was requested.
     */
    final public function isEmpty(): bool
    {
        return [] === $this->fields;
    }

    /**
     * Whether the result is sorted by the given field.
     */
    final public function has(string $field): bool
    {
        return array_any($this->fields, static fn (SortField $candidate): bool => $candidate->field === $field);
    }

    /**
     * The direction requested for a field, or null when the result is not sorted
     * by it.
     */
    final public function directionFor(string $field): ?SortDirection
    {
        $sortField = array_find(
            $this->fields,
            static fn (SortField $candidate): bool => $candidate->field === $field,
        );

        return $sortField?->direction;
    }

    /**
     * Parses the standard JSON:API "sort=-a,b" parameter into sort fields,
     * resolving ascending/descending from the leading "-". The format is fixed
     * here; the subclass decides how to type the result.
     *
     * @param array<array-key, mixed> $attributes
     *
     * @return SortField[]
     */
    final protected static function parse(array $attributes): array
    {
        if (!isset($attributes['sort']) || !\is_string($attributes['sort'])) {
            return [];
        }

        return array_map(
            static fn (string $field): SortField => str_starts_with($field, '-')
                ? SortField::desc(substr($field, 1))
                : SortField::asc($field),
            PathNavigator::segments($attributes['sort'], ','),
        );
    }

    /**
     * @param array<array-key, mixed> $attributes
     */
    #[\Override]
    abstract public static function deserialize(array $attributes): static;

    /**
     * @return array{sort?: string}
     *
     * @throws UnsupportedSerializationException always; query value objects are input only
     */
    #[\Override]
    final public function serialize(): array
    {
        throw UnsupportedSerializationException::for('A sort value object');
    }
}
