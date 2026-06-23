<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Error;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Messages\Error\Definition\SourceType;

final class SourceTypeTest extends TestCase
{
    #[Test]
    public function pointer_uses_a_slash_separator(): void
    {
        self::assertSame('/', SourceType::POINTER->separator());
    }

    #[Test]
    public function parameter_and_header_use_a_dot_separator(): void
    {
        self::assertSame('.', SourceType::PARAMETER->separator());
        self::assertSame('.', SourceType::HEADER->separator());
    }

    #[Test]
    public function its_value_is_the_jsonapi_source_member(): void
    {
        self::assertSame('pointer', SourceType::POINTER->value);
        self::assertSame('parameter', SourceType::PARAMETER->value);
        self::assertSame('header', SourceType::HEADER->value);
    }
}
