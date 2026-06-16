<?php

declare(strict_types=1);

namespace Psr\Messages\Error\Definition;

/**
 * A machine-readable error code, e.g. "malformed_content". Implemented by
 * string-backed enums, so different parts of the system (and downstream
 * libraries) can define their own codes while remaining polymorphic. The code
 * value is the enum's backing value, available via ->value.
 */
interface ErrorCodeInterface extends \BackedEnum
{
}
