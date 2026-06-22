<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Unit\JsonApi\Query;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Messages\Exception\UnexpectedStateException;
use Psr\Messages\JsonApi\Query\Definition\Page;

final class PageTest extends TestCase
{
    #[Test]
    public function it_parses_number_and_size(): void
    {
        $page = Page::deserialize(['page' => ['number' => '3', 'size' => '50']]);

        self::assertSame(3, $page->number);
        self::assertSame(50, $page->size);
    }

    #[Test]
    public function it_defaults_number_to_one_and_size_to_twenty(): void
    {
        $page = Page::deserialize(['page' => []]);

        self::assertSame(1, $page->number);
        self::assertSame(20, $page->size);
    }

    #[Test]
    public function it_defaults_when_no_page_parameter_is_given(): void
    {
        $page = Page::deserialize([]);

        self::assertSame(1, $page->number);
        self::assertSame(20, $page->size);
    }

    #[Test]
    public function it_derives_the_zero_based_offset(): void
    {
        $page = Page::deserialize(['page' => ['number' => '3', 'size' => '20']]);

        self::assertSame(40, $page->offset());
    }

    #[Test]
    public function it_throws_when_number_is_not_numeric(): void
    {
        $this->expectException(UnexpectedStateException::class);

        Page::deserialize(['page' => ['number' => 'x', 'size' => '20']]);
    }
}
