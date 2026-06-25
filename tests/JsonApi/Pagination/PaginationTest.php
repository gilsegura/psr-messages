<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\JsonApi\Pagination;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Messages\JsonApi\Pagination\Pagination;
use Psr\Messages\JsonApi\Query\Definition\Page;
use Psr\Messages\Link\Definition\Href;

final class PaginationTest extends TestCase
{
    #[Test]
    public function it_builds_the_page_meta(): void
    {
        $meta = new Pagination()->meta(new Page(2, 10), 35);

        self::assertSame([
            'page' => ['number' => 2, 'size' => 10, 'total' => 35, 'pages' => 4],
        ], $meta);
    }

    #[Test]
    public function it_builds_self_first_last_prev_next_links_in_the_middle(): void
    {
        $links = new Pagination()->links(new Page(2, 10), 35, Href::fromRelative('/things'));

        // 5 links on a middle page: self, first, last, prev, next.
        self::assertCount(5, $links);
    }

    #[Test]
    public function it_omits_prev_on_the_first_page_and_next_on_the_last(): void
    {
        $first = new Pagination()->links(new Page(1, 10), 35, Href::fromRelative('/things'));
        $last = new Pagination()->links(new Page(4, 10), 35, Href::fromRelative('/things'));

        // First page: self, first, last, next (no prev).
        self::assertCount(4, $first);
        // Last page: self, first, last, prev (no next).
        self::assertCount(4, $last);
    }
}
