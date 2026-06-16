<?php

declare(strict_types=1);

namespace Psr\Messages\Schema;

use Psr\Messages\Schema\Exception\UnresolvedSchemaException;

/**
 * Resolves and deserializes raw input data (a request body or query parameters)
 * into the typed schema object that applies to it, out of the schemas an
 * endpoint accepts.
 */
interface SchemaResolverInterface
{
    /**
     * @param array<array-key, mixed> $data
     *
     * @return SchemaInterface<array<array-key, mixed>>
     *
     * @throws UnresolvedSchemaException
     */
    public function resolve(array $data): SchemaInterface;
}
