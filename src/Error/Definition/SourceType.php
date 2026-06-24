<?php

declare(strict_types=1);

namespace Psr\Messages\Error\Definition;

/**
 * The kind of request part an error points to, mapped to the JSON:API error
 * "source" member name: a body uses a JSON Pointer, a query parameter uses its
 * name, and a header uses its name. The backing value is the source member name.
 */
enum SourceType: string implements SourceTypeInterface
{
    case POINTER = 'pointer';
    case PARAMETER = 'parameter';
    case HEADER = 'header';

    #[\Override]
    public function separator(): string
    {
        return match ($this) {
            self::POINTER => '/',
            self::PARAMETER, self::HEADER => '.',
        };
    }
}
