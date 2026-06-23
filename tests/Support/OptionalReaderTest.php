<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Support;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Messages\Exception\UnexpectedStateException;
use Psr\Messages\Support\OptionalReader;

final class OptionalReaderTest extends TestCase
{
    #[Test]
    public function string_reads_a_present_field(): void
    {
        $optional = OptionalReader::string(['title' => 'Hello'], 'title');

        self::assertTrue($optional->isPresent());
        self::assertSame('Hello', $optional->get());
    }

    #[Test]
    public function string_is_absent_when_the_field_is_missing(): void
    {
        $optional = OptionalReader::string(['body' => 'x'], 'title');

        self::assertFalse($optional->isPresent());
    }

    #[Test]
    public function string_throws_when_a_present_field_is_not_a_string(): void
    {
        $this->expectException(UnexpectedStateException::class);

        OptionalReader::string(['title' => 123], 'title');
    }

    #[Test]
    public function int_reads_a_present_integer(): void
    {
        $optional = OptionalReader::int(['size' => 10], 'size');

        self::assertTrue($optional->isPresent());
        self::assertSame(10, $optional->get());
    }

    #[Test]
    public function bool_reads_a_present_boolean(): void
    {
        $optional = OptionalReader::bool(['featured' => true], 'featured');

        self::assertTrue($optional->isPresent());
        self::assertTrue($optional->get());
    }

    #[Test]
    public function nested_returns_the_inner_array(): void
    {
        $nested = OptionalReader::nested(['data' => ['id' => '1']], 'data');

        self::assertSame(['id' => '1'], $nested);
    }

    #[Test]
    public function nested_returns_an_empty_array_when_absent(): void
    {
        self::assertSame([], OptionalReader::nested(['other' => 1], 'data'));
    }
}
