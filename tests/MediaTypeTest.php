<?php

declare(strict_types=1);

namespace Psr\Messages\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Messages\Exception\UnsupportedMediaTypeException;
use Psr\Messages\MediaType;

final class MediaTypeTest extends TestCase
{
    #[Test]
    public function it_resolves_json_api_from_a_header_line(): void
    {
        self::assertSame(MediaType::JSON_API, MediaType::fromHeaderLine('application/vnd.api+json'));
    }

    #[Test]
    public function it_resolves_json_from_a_header_line_with_parameters(): void
    {
        self::assertSame(MediaType::JSON, MediaType::fromHeaderLine('application/json; charset=utf-8'));
    }

    #[Test]
    public function it_throws_on_an_unsupported_media_type(): void
    {
        $this->expectException(UnsupportedMediaTypeException::class);

        MediaType::fromHeaderLine('text/html');
    }

    #[Test]
    public function it_compares_media_types(): void
    {
        self::assertTrue(MediaType::JSON->equals(MediaType::JSON));
        self::assertFalse(MediaType::JSON->equals(MediaType::JSON_API));
    }
}
