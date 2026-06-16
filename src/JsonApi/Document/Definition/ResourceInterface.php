<?php

declare(strict_types=1);

namespace Psr\Messages\JsonApi\Document\Definition;

use Serializer\SerializableInterface;

/**
 * A JSON:API resource object. Every resource is identifiable (type + id),
 * carries attributes, and serializes itself to its JSON:API form.
 * Relationships, links and meta are optional and declared via their own
 * interfaces (HasOneRelationship, HasManyRelationships, HasLinks, HasMeta).
 *
 * @extends SerializableInterface<array<string, mixed>>
 */
interface ResourceInterface extends HasResourceIdentifierInterface, HasAttributesInterface, SerializableInterface
{
}
