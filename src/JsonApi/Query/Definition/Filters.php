<?php

declare(strict_types=1);

namespace Psr\Messages\JsonApi\Query\Definition;

use Psr\Messages\Exception\UnexpectedStateException;
use Psr\Messages\Exception\UnsupportedSerializationException;
use Serializer\SerializableInterface;

/**
 * JSON:API filtering: filter[field]=value pairs, e.g. "filter[status]=active".
 * Values are kept as strings; the meaning of each filter is left to the
 * application.
 *
 * @implements SerializableInterface<array{filter?: array<string, string>}>
 */
final readonly class Filters implements SerializableInterface
{
    /** @var Filter[] */
    public array $filters;

    public function __construct(Filter ...$filters)
    {
        $this->filters = $filters;
    }

    /**
     * @param array<string, mixed> $attributes
     */
    #[\Override]
    public static function deserialize(array $attributes): static
    {
        if (!isset($attributes['filter'])) {
            return new self();
        }

        $filter = $attributes['filter'];

        if (!\is_array($filter)) {
            throw UnexpectedStateException::reason('the filter query parameter must be an array.');
        }

        if (!array_all($filter, static fn (mixed $value, mixed $field): bool => \is_string($field) && \is_string($value))) {
            throw UnexpectedStateException::reason('each filter must be a string keyed by a string field.');
        }

        /** @var array<string, string> $filter */
        $filters = array_map(
            static fn (string $field): Filter => new Filter($field, $filter[$field]),
            array_keys($filter),
        );

        return new self(...$filters);
    }

    /**
     * @throws UnsupportedSerializationException always; query value objects are input only
     */
    #[\Override]
    public function serialize(): array
    {
        throw UnsupportedSerializationException::for('A filters value object');
    }
}
