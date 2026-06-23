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
     * Whether this schema applies to the given input data. The data has already
     * been validated against the endpoint's JSON Schema, so this only
     * discriminates between the shapes an endpoint accepts; it never re-validates
     * integrity. It receives the same typed data as deserialize().
     *
     * @param TAttributes $data
     */
    public static function supports(array $data): bool;
}
