<?php

declare(strict_types=1);

namespace Psr\Messages\JsonApi\Document\Definition;

/**
 * A JSON:API resource type (the "type" member). Implemented by string-backed
 * enums so each application defines its own types while remaining polymorphic.
 * The backing value is the type name, available via ->value.
 */
interface ResourceTypeInterface extends \BackedEnum
{
    public function equals(ResourceTypeInterface $type): bool;
}
