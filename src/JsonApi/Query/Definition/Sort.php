<?php

declare(strict_types=1);

namespace Psr\Messages\JsonApi\Query\Definition;

use Psr\Messages\Exception\UnexpectedStateException;
use Psr\Messages\Exception\UnsupportedSerializationException;
use Serializer\SerializableInterface;

/**
 * JSON:API sorting: a comma-separated list of fields, each optionally prefixed
 * with "-" for descending order, e.g. "sort=-created,name".
 *
 * @implements SerializableInterface<array{sort?: string}>
 */
final readonly class Sort implements SerializableInterface
{
    /** @var SortField[] */
    public array $fields;

    public function __construct(
        SortField ...$fields,
    ) {
        $this->fields = $fields;
    }

    /**
     * @param array<string, mixed> $attributes
     */
    #[\Override]
    public static function deserialize(array $attributes): static
    {
        if (!isset($attributes['sort'])) {
            return new self();
        }

        if (
            !\is_string($attributes['sort'])
            || '' === $attributes['sort']
        ) {
            throw UnexpectedStateException::reason('the sort query parameter must be a non-empty string.');
        }

        $factory = static fn (string $field): SortField => str_starts_with($field, '-')
            ? SortField::desc(substr($field, 1))
            : SortField::asc($field);

        $fields = array_values(array_filter(
            array_map(trim(...), explode(',', $attributes['sort'])),
            static fn (string $field): bool => '' !== $field,
        ));

        return new self(...array_map($factory, $fields));
    }

    /**
     * @throws UnsupportedSerializationException always; query value objects are input only
     */
    #[\Override]
    public function serialize(): array
    {
        throw UnsupportedSerializationException::for('A sort value object');
    }
}
