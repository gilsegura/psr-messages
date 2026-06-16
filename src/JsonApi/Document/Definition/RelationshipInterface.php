<?php

declare(strict_types=1);

namespace Psr\Messages\JsonApi\Document\Definition;

use Serializer\SerializableInterface;

/**
 * A JSON:API relationship. Serializes to its "{data: linkage}" form, where the
 * linkage is a resource identifier (to-one) or a list of them (to-many).
 *
 * @extends SerializableInterface<array<string, mixed>>
 */
interface RelationshipInterface extends SerializableInterface
{
}
