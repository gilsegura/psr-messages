<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Unit\Link;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Messages\Link\Definition\LinkContext;
use Psr\Messages\Link\Definition\LinkType;

final class LinkTypeTest extends TestCase
{
    #[Test]
    public function pagination_links_are_paginators(): void
    {
        self::assertTrue(LinkType::FIRST->isPaginator());
        self::assertTrue(LinkType::LAST->isPaginator());
        self::assertTrue(LinkType::PREV->isPaginator());
        self::assertTrue(LinkType::NEXT->isPaginator());
    }

    #[Test]
    public function non_pagination_links_are_not_paginators(): void
    {
        self::assertFalse(LinkType::SELF->isPaginator());
        self::assertFalse(LinkType::RELATED->isPaginator());
        self::assertFalse(LinkType::ABOUT->isPaginator());
    }

    #[Test]
    public function self_belongs_to_resource_and_error_contexts(): void
    {
        self::assertContains(LinkContext::RESOURCE, LinkType::SELF->contexts());
        self::assertContains(LinkContext::ERROR, LinkType::SELF->contexts());
    }

    #[Test]
    public function about_belongs_to_the_error_context(): void
    {
        self::assertSame([LinkContext::ERROR], LinkType::ABOUT->contexts());
    }
}
