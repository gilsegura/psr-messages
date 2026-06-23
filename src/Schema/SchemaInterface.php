<?php

declare(strict_types=1);

namespace Psr\Messages\Schema;

use Serializer\SerializableInterface;

/**
 * A self-resolving schema for incoming request data, either a body or query
 * parameters.
 *
 * A schema is an input concept: it declares whether raw input data is its own
 * (`supports`) and, being serializable, turns that data into a typed object
 * through `deserialize`. An endpoint that accepts several shapes exposes one
 * schema per shape; the resolver picks the one whose `supports` matches.
 *
 * Responses are not schemas: outgoing objects implement SerializableInterface
 * directly, since there is nothing to resolve on the way out.
 *
 * @template TAttributes of array
 *
 * @extends SerializableInterface<TAttributes>
 */
interface SchemaInterface extends SerializableInterface
{
    /**
     * Whether this schema applies to the given raw input data.
     *
     * @param TAttributes $data
     */
    public static function supports(array $data): bool;
}
