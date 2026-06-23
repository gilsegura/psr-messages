<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Error;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Messages\Error\Definition\Source;
use Psr\Messages\Error\Definition\SourceType;
use Psr\Messages\Exception\UnexpectedStateException;

final class SourceTest extends TestCase
{
    #[Test]
    public function it_builds_a_pointer_source_from_a_validation_error(): void
    {
        $source = Source::forPointer(['pointer' => '/data/attributes/title']);

        self::assertSame(SourceType::POINTER, $source->type);
        self::assertSame('/data/attributes/title', $source->path);
    }

    #[Test]
    public function it_builds_a_parameter_source_stripping_a_leading_slash(): void
    {
        $source = Source::forParameter(['property' => '/page']);

        self::assertSame(SourceType::PARAMETER, $source->type);
        self::assertSame('page', $source->path);
    }

    #[Test]
    public function it_builds_a_header_source(): void
    {
        $source = Source::forHeader(['property' => 'authorization']);

        self::assertSame(SourceType::HEADER, $source->type);
        self::assertSame('authorization', $source->path);
    }

    #[Test]
    public function it_throws_when_the_pointer_field_is_missing(): void
    {
        $this->expectException(UnexpectedStateException::class);

        Source::forPointer([]);
    }

    #[Test]
    public function it_splits_a_pointer_source_into_segments_with_slash(): void
    {
        $source = Source::forPointer(['pointer' => '/data/attributes/title']);

        self::assertSame(['data', 'attributes', 'title'], $source->segments());
    }

    #[Test]
    public function it_splits_a_parameter_source_into_segments_with_dot(): void
    {
        $source = Source::forParameter(['property' => 'page.size']);

        self::assertSame(['page', 'size'], $source->segments());
    }
}
