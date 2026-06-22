<?php

declare(strict_types=1);

namespace Psr\Messages\Link\Definition;

/**
 * The standard JSON:API link types: the keys a link can appear under in a
 * "links" object. Downstream libraries can define their own string-backed enums
 * implementing LinkTypeInterface for additional types.
 */
enum LinkType: string implements LinkTypeInterface
{
    case SELF = 'self';
    case RELATED = 'related';
    case DESCRIBEDBY = 'describedby';
    case ABOUT = 'about';
    case FIRST = 'first';
    case LAST = 'last';
    case PREV = 'prev';
    case NEXT = 'next';
}
