<?php

declare(strict_types=1);

namespace Psr\Messages\JsonApi\Document\Definition;

/**
 * A JSON:API relationship name (the key a relationship appears under, and the
 * include path that requests it). Implemented by string-backed enums so each
 * application names its own relationships while staying polymorphic; the backing
 * value is the relationship name (e.g. "author", "tags"), available via ->value.
 */
interface RelationshipNameInterface extends \BackedEnum
{
    public function equals(RelationshipNameInterface $name): bool;
}
