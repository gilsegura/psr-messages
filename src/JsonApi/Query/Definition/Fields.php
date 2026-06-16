<?php

declare(strict_types=1);

namespace Psr\Messages\JsonApi\Query\Definition;

use Psr\Messages\Exception\UnexpectedStateException;
use Psr\Messages\Exception\UnsupportedSerializationException;
use Serializer\SerializableInterface;

/**
 * JSON:API sparse fieldsets: fields[type]=a,b pairs that limit which fields are
 * returned for each resource type, e.g. "fields[articles]=title,body". The
 * field list is comma-separated; applying it is left to the application.
 *
 * @implements SerializableInterface<array{fields?: array<string, string>}>
 */
final readonly class Fields implements SerializableInterface
{
    /** @var Field[] */
    public array $fields;

    public function __construct(Field ...$fields)
    {
        $this->fields = $fields;
    }

    /**
     * @param array<string, mixed> $attributes
     */
    #[\Override]
    public static function deserialize(array $attributes): static
    {
        if (!isset($attributes['fields'])) {
            return new self();
        }

        $fields = $attributes['fields'];

        if (!\is_array($fields)) {
            throw UnexpectedStateException::reason('the fields query parameter must be an array.');
        }

        if (!array_all($fields, static fn (mixed $value, mixed $type): bool => \is_string($type) && \is_string($value))) {
            throw UnexpectedStateException::reason('each fieldset must be a string keyed by a string type.');
        }

        /** @var array<string, string> $fields */
        $fieldsets = array_map(
            static fn (string $type): Field => new Field($type, ...self::split($fields[$type])),
            array_keys($fields),
        );

        return new self(...$fieldsets);
    }

    /**
     * @return string[]
     */
    private static function split(string $list): array
    {
        return array_values(array_filter(
            array_map(trim(...), explode(',', $list)),
            static fn (string $field): bool => '' !== $field,
        ));
    }

    /**
     * @throws UnsupportedSerializationException always; query value objects are input only
     */
    #[\Override]
    public function serialize(): array
    {
        throw UnsupportedSerializationException::for('A fields value object');
    }
}
