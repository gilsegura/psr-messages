<?php

declare(strict_types=1);

namespace Psr\Messages\Link\Definition;

/**
 * A JSON:API link type (the key under "links"). Implemented by string-backed
 * enums so the system and downstream libraries can define their own link types
 * while remaining polymorphic. The backing value is the link key (e.g. "self",
 * "next"), available via ->value.
 */
interface LinkTypeInterface extends \BackedEnum
{
}
