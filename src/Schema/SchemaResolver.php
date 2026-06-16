<?php

declare(strict_types=1);

namespace Psr\Messages\Schema;

use Psr\Messages\Schema\Exception\UnresolvedSchemaException;

/**
 * Resolves input data against an ordered set of schemas, deserializing it with the
 * first schema whose `supports` matches. Schemas are tried in registration
 * order, so a catch-all schema (one whose `supports` always returns true) must
 * be registered last.
 */
final readonly class SchemaResolver implements SchemaResolverInterface
{
    /** @var array<class-string<SchemaInterface<array<array-key, mixed>>>> */
    private array $schemas;

    /**
     * @param class-string<SchemaInterface<array<array-key, mixed>>> ...$schemas
     */
    public function __construct(string ...$schemas)
    {
        $this->schemas = $schemas;
    }

    #[\Override]
    public function resolve(array $data): SchemaInterface
    {
        $schema = array_find(
            $this->schemas,
            static fn (string $schema): bool => $schema::supports($data),
        ) ?? throw UnresolvedSchemaException::forData($data);

        return $schema::deserialize($data);
    }
}
