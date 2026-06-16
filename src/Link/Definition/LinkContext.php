<?php

declare(strict_types=1);

namespace Psr\Messages\Link\Definition;

/**
 * The contexts a link type can belong to. A single type may apply to several
 * (e.g. "self" and "describedby" appear in both resource and error documents).
 */
enum LinkContext
{
    case PAGINATION;
    case RESOURCE;
    case ERROR;
}
