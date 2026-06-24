<?php

declare(strict_types=1);

namespace Psr\Messages\JsonApi\Document\Definition;

use Serializer\SerializableInterface;

/**
 * A JSON:API resource object: identifiable (type + id), carrying an attributes
 * payload, and able to serialize itself to its JSON:API form. Relationships,
 * links and meta are optional, declared via their own interfaces (HasLinks,
 * HasMeta) or exposed directly by the concrete resource.
 *
 * @extends SerializableInterface<array<string, mixed>>
 */
interface ResourceInterface extends HasResourceIdentifierInterface, SerializableInterface
{
    /**
     * @var SerializableInterface<array<string, mixed>>
     */
    public SerializableInterface $attributes { get; }
}
