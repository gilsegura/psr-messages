<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Error;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Messages\Error\Definition\Error;
use Psr\Messages\Error\Definition\ErrorCode;
use Psr\Messages\Error\Definition\Source;
use Psr\Messages\Error\Definition\SourceType;
use Psr\Messages\Exception\MissingErrorSourceException;

final class ErrorTest extends TestCase
{
    #[Test]
    public function it_describes_a_generic_throwable_as_an_internal_error(): void
    {
        $error = Error::fromThrowable(new \RuntimeException('boom'));

        self::assertSame(ErrorCode::INTERNAL_ERROR, $error->errorCode());
        self::assertSame('boom', $error->detail());
    }

    #[Test]
    public function it_has_no_source_by_default(): void
    {
        $error = Error::fromThrowable(new \RuntimeException('boom'));

        self::assertFalse($error->hasSource());
    }

    #[Test]
    public function it_throws_when_accessing_a_missing_source(): void
    {
        $error = Error::fromThrowable(new \RuntimeException('boom'));

        $this->expectException(MissingErrorSourceException::class);

        $error->source();
    }

    #[Test]
    public function with_source_returns_a_copy_carrying_the_source(): void
    {
        $error = new Error(ErrorCode::MALFORMED_CONTENT, 'Title', 'Detail');
        $source = new Source(SourceType::POINTER, '/data');

        $withSource = $error->withSource($source);

        self::assertFalse($error->hasSource());
        self::assertTrue($withSource->hasSource());
        self::assertSame($source, $withSource->source());
    }

    #[Test]
    public function with_detail_returns_a_copy_with_the_new_detail(): void
    {
        $error = new Error(ErrorCode::MALFORMED_CONTENT, 'Title', 'Old');

        $withDetail = $error->withDetail('New');

        self::assertSame('Old', $error->detail());
        self::assertSame('New', $withDetail->detail());
    }

    #[Test]
    public function with_meta_returns_a_copy_with_meta(): void
    {
        $error = new Error(ErrorCode::INTERNAL_ERROR, 'Title', 'Detail');

        $withMeta = $error->withMeta(['retryable' => false]);

        self::assertSame([], $error->meta);
        self::assertSame(['retryable' => false], $withMeta->meta);
    }
}
