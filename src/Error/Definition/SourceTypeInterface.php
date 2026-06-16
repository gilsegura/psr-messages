<?php

declare(strict_types=1);

namespace Psr\Messages\Error\Definition;

/**
 * The kind of source an error points to. Implemented by string-backed enums so
 * the system (and downstream libraries) can define their own source types while
 * remaining polymorphic. The backing value is the JSON:API "source" member name
 * (e.g. "pointer", "parameter", "header"), available via ->value.
 */
interface SourceTypeInterface extends \BackedEnum
{
    /**
     * The separator used to split a path of this source type into segments,
     * e.g. "/" for a JSON Pointer.
     *
     * @return non-empty-string
     */
    public function separator(): string;
}
