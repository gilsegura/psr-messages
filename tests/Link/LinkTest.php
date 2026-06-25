<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Link;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Messages\Exception\InvalidHrefException;
use Psr\Messages\Link\Definition\Href;
use Psr\Messages\Link\Definition\Link;
use Psr\Messages\Link\Definition\LinkType;

final class LinkTest extends TestCase
{
    #[Test]
    public function it_accepts_a_valid_url(): void
    {
        $href = Href::fromAbsolute('https://api.example.com/posts/1');

        self::assertSame('https://api.example.com/posts/1', $href->value);
    }

    #[Test]
    public function it_rejects_an_invalid_url(): void
    {
        $this->expectException(InvalidHrefException::class);

        Href::fromAbsolute('not a url');
    }

    #[Test]
    public function it_serializes_a_list_of_links_to_a_type_href_map(): void
    {
        $links = [
            new Link(LinkType::SELF, Href::fromAbsolute('https://api.example.com/posts/1')),
            new Link(LinkType::NEXT, Href::fromAbsolute('https://api.example.com/posts?page=2')),
        ];

        self::assertSame([
            'self' => 'https://api.example.com/posts/1',
            'next' => 'https://api.example.com/posts?page=2',
        ], Link::toArray($links));
    }

    #[Test]
    public function it_builds_standard_links_through_named_constructors(): void
    {
        $href = Href::fromAbsolute('https://api.example.com/articles?page[number]=1&page[size]=10');

        self::assertSame(LinkType::SELF, Link::self($href)->type);
        self::assertSame(LinkType::FIRST, Link::first($href)->type);
        self::assertSame(LinkType::LAST, Link::last($href)->type);
        self::assertSame(LinkType::PREV, Link::prev($href)->type);
        self::assertSame(LinkType::NEXT, Link::next($href)->type);
    }
}
