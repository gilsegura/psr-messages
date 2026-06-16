<?php

declare(strict_types=1);

namespace Psr\Messages\JsonApi\Document\Definition;

use Serializer\SerializableInterface;

/**
 * Implemented by resources that carry an attributes payload.
 */
interface HasAttributesInterface
{
    /**
     * @return SerializableInterface<array<string, mixed>>
     */
    public function attributes(): SerializableInterface;

    /**
     * @param SerializableInterface<array<string, mixed>> $attributes
     */
    public function withAttributes(SerializableInterface $attributes): static;
}
