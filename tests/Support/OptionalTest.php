<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Support;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Messages\Exception\UnexpectedStateException;
use Psr\Messages\Support\Optional;

final class OptionalTest extends TestCase
{
    #[Test]
    public function a_present_value_is_present_and_readable(): void
    {
        $optional = Optional::of('hello');

        self::assertTrue($optional->isPresent());
        self::assertSame('hello', $optional->get());
    }

    #[Test]
    public function a_present_null_is_still_present(): void
    {
        $optional = Optional::of(null);

        self::assertTrue($optional->isPresent());
        // a present null is returned, not the fallback
        self::assertNull($optional->orElse('fallback'));
    }

    #[Test]
    public function an_absent_value_is_not_present(): void
    {
        $optional = Optional::absent();

        self::assertFalse($optional->isPresent());
    }

    #[Test]
    public function reading_an_absent_value_throws(): void
    {
        $this->expectException(UnexpectedStateException::class);

        Optional::absent()->get();
    }

    #[Test]
    public function or_else_returns_the_value_when_present(): void
    {
        self::assertSame('x', Optional::of('x')->orElse('default'));
    }

    #[Test]
    public function or_else_returns_the_default_when_absent(): void
    {
        self::assertSame('default', Optional::absent()->orElse('default'));
    }
}
