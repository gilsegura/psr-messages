<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Link;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Messages\Exception\InvalidHrefException;
use Psr\Messages\Link\Definition\Href;

final class HrefTest extends TestCase
{
    #[Test]
    public function it_accepts_an_absolute_url(): void
    {
        $href = Href::fromAbsolute('https://api.example.com/articles');

        self::assertSame('https://api.example.com/articles', $href->value);
    }

    #[Test]
    public function it_rejects_a_non_absolute_url(): void
    {
        $this->expectException(InvalidHrefException::class);

        Href::fromAbsolute('/articles');
    }

    #[Test]
    public function it_accepts_a_relative_reference_rooted_at_slash(): void
    {
        $href = Href::fromRelative('/articles');

        self::assertSame('/articles', $href->value);
    }

    #[Test]
    public function it_rejects_a_relative_reference_not_rooted_at_slash(): void
    {
        $this->expectException(InvalidHrefException::class);

        Href::fromRelative('articles');
    }

    #[Test]
    public function it_appends_query_params_to_a_relative_href(): void
    {
        $href = Href::fromRelative('/articles')->withQueryParams(['page' => ['number' => 2, 'size' => 10]]);

        self::assertStringStartsWith('/articles?', $href->value);
        self::assertSame(['number' => '2', 'size' => '10'], $this->pageParams($href->value));
    }

    #[Test]
    public function it_keeps_existing_query_params_when_appending(): void
    {
        $href = Href::fromAbsolute('https://api.example.com/articles?filter=active')
            ->withQueryParams(['page' => ['number' => 1]]);

        parse_str((string) parse_url($href->value, \PHP_URL_QUERY), $query);

        self::assertSame('active', $query['filter'] ?? null);
        self::assertSame(['number' => '1'], $query['page'] ?? null);
    }

    #[Test]
    public function it_returns_the_same_href_when_no_params_are_given(): void
    {
        $href = Href::fromRelative('/articles');

        self::assertSame('/articles', $href->withQueryParams([])->value);
    }

    /**
     * @return array<string, string>
     */
    private function pageParams(string $url): array
    {
        parse_str((string) parse_url($url, \PHP_URL_QUERY), $query);

        /** @var array<string, string> $page */
        $page = $query['page'] ?? [];

        return $page;
    }
}
