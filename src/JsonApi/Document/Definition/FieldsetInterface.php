<?php

declare(strict_types=1);

namespace Psr\Messages\JsonApi\Document\Definition;

use Serializer\SerializableInterface;

/**
 * The sparse-fieldset capability a resource needs to trim itself: whether a
 * fieldset was requested for a type, and applying it to an attributes payload.
 *
 * Declared on the document side so a resource (ResourceObject) depends only on
 * this contract, not on the concrete query object. The query layer's
 * AbstractFields implements it, inverting what would otherwise be a
 * Document -> Query dependency.
 */
interface FieldsetInterface
{
    /**
     * Whether a fieldset was requested for the given resource type.
     */
    public function has(ResourceTypeInterface $type): bool;

    /**
     * Trims the attributes to the fieldset requested for the type, or returns
     * them untouched when no fieldset constrains that type.
     *
     * @param SerializableInterface<array<string, mixed>> $attributes
     *
     * @return array<string, mixed>
     */
    public function apply(ResourceTypeInterface $type, SerializableInterface $attributes): array;
}
