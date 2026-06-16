<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Unit\Support;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Messages\Support\ClassName;

final class ClassNameTest extends TestCase
{
    #[Test]
    public function it_returns_the_short_name(): void
    {
        self::assertSame('ClassNameTest', ClassName::short(self::class));
    }

    #[Test]
    public function it_turns_a_class_name_into_a_title(): void
    {
        self::assertSame('Class name test', ClassName::toTitle(self::class));
    }
}
