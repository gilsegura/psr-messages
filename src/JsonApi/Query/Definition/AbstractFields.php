<?php

declare(strict_types=1);

namespace Psr\Messages\JsonApi\Query\Definition;

use Psr\Messages\Exception\UnsupportedSerializationException;
use Psr\Messages\JsonApi\Document\Definition\FieldsetInterface;
use Psr\Messages\JsonApi\Document\Definition\ResourceTypeInterface;
use Psr\Messages\Support\PathNavigator;
use Serializer\SerializableInterface;

/**
 * Base for JSON:API sparse fieldsets: fields[type]=a,b pairs limiting which
 * fields are returned per resource type, e.g. "fields[articles]=title,body".
 * This base owns the fixed JSON:API format and the operations over the parsed
 * fieldsets (forType/has/apply); the concrete subclass in the consuming library
 * types the resource's fieldsets. Which fields are permitted is enforced by the
 * endpoint's JSON Schema before parsing.
 *
 * @implements SerializableInterface<array{fields?: array<string, string>}>
 */
abstract readonly class AbstractFields implements FieldsetInterface, SerializableInterface
{
    /** @var Field[] */
    public array $fields;

    final public function __construct(Field ...$fields)
    {
        $this->fields = $fields;
    }

    /**
     * The fieldset requested for a resource type, or null when the type was not
     * constrained (meaning every field should be returned).
     */
    final public function forType(ResourceTypeInterface $type): ?Field
    {
        return array_find(
            $this->fields,
            static fn (Field $field): bool => $field->type->equals($type),
        );
    }

    /**
     * Whether a fieldset was requested for the given type.
     */
    #[\Override]
    final public function has(ResourceTypeInterface $type): bool
    {
        return $this->forType($type) instanceof Field;
    }

    /**
     * Applies the fieldset for a type to an attributes payload: keeps only the
     * requested fields, or returns the attributes unchanged when the type was not
     * constrained.
     *
     * @param SerializableInterface<array<string, mixed>> $attributes
     *
     * @return array<string, mixed>
     */
    #[\Override]
    final public function apply(ResourceTypeInterface $type, SerializableInterface $attributes): array
    {
        $field = $this->forType($type);

        if (!$field instanceof Field) {
            return $attributes->serialize();
        }

        return $field->keep($attributes->serialize());
    }

    /**
     * Parses the standard JSON:API "fields[type]=a,b" parameter into fieldsets,
     * resolving each type name through the given resolver. The format is fixed
     * here; the subclass supplies how a type name maps to its typed resource type.
     *
     * @param array<array-key, mixed>                 $attributes
     * @param callable(string): ResourceTypeInterface $type
     *
     * @return Field[]
     */
    final protected static function parse(array $attributes, callable $type): array
    {
        if (!isset($attributes['fields']) || !\is_array($attributes['fields'])) {
            return [];
        }

        /** @var array<string, string> $fields */
        $fields = $attributes['fields'];

        return array_map(
            static fn (string $name): Field => new Field($type($name), ...PathNavigator::segments($fields[$name], ',')),
            array_keys($fields),
        );
    }

    /**
     * @param array<array-key, mixed> $attributes
     */
    #[\Override]
    abstract public static function deserialize(array $attributes): static;

    /**
     * @return array{fields?: array<string, string>}
     *
     * @throws UnsupportedSerializationException always; query value objects are input only
     */
    #[\Override]
    final public function serialize(): array
    {
        throw UnsupportedSerializationException::for('A fields value object');
    }
}
