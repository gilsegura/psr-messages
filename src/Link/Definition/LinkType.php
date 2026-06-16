<?php

declare(strict_types=1);

namespace Psr\Messages\Link\Definition;

/**
 * The JSON:API link types, each declaring the contexts it belongs to.
 * Downstream libraries can define their own string-backed enums implementing
 * LinkTypeInterface.
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

    /**
     * @return LinkContext[]
     */
    #[\Override]
    public function contexts(): array
    {
        return match ($this) {
            self::FIRST, self::LAST, self::PREV, self::NEXT => [LinkContext::PAGINATION],
            self::RELATED => [LinkContext::RESOURCE],
            self::ABOUT => [LinkContext::ERROR],
            self::SELF, self::DESCRIBEDBY => [LinkContext::RESOURCE, LinkContext::ERROR],
        };
    }

    public function isPaginator(): bool
    {
        return \in_array(LinkContext::PAGINATION, $this->contexts(), true);
    }
}
