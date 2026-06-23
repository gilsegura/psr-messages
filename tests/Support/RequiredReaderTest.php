<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Support;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Messages\Exception\UnexpectedStateException;
use Psr\Messages\Support\RequiredReader;

final class RequiredReaderTest extends TestCase
{
    #[Test]
    public function string_reads_a_present_field(): void
    {
        self::assertSame('Hello', RequiredReader::string(['title' => 'Hello'], 'title'));
    }

    #[Test]
    public function string_throws_when_the_field_is_missing(): void
    {
        $this->expectException(UnexpectedStateException::class);

        RequiredReader::string(['body' => 'x'], 'title');
    }

    #[Test]
    public function string_throws_when_the_field_is_not_a_string(): void
    {
        $this->expectException(UnexpectedStateException::class);

        RequiredReader::string(['title' => 123], 'title');
    }

    #[Test]
    public function int_reads_a_present_integer(): void
    {
        self::assertSame(10, RequiredReader::int(['size' => 10], 'size'));
    }

    #[Test]
    public function nested_reads_a_present_object(): void
    {
        self::assertSame(['id' => '1'], RequiredReader::nested(['data' => ['id' => '1']], 'data'));
    }

    #[Test]
    public function nested_throws_when_the_object_is_missing(): void
    {
        $this->expectException(UnexpectedStateException::class);

        RequiredReader::nested(['other' => 1], 'data');
    }
}
