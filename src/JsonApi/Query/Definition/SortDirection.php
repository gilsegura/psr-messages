<?php

declare(strict_types=1);

namespace Psr\Messages\JsonApi\Query\Definition;

enum SortDirection: string
{
    case ASC = 'asc';
    case DESC = 'desc';
}
