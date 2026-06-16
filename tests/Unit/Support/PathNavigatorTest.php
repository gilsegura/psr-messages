<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Unit\Support;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Messages\Support\PathNavigator;

final class PathNavigatorTest extends TestCase
{
    #[Test]
    public function it_splits_a_pointer_path_into_segments(): void
    {
        self::assertSame(['data', 'attributes', 'title'], PathNavigator::segments('/data/attributes/title', '/'));
    }

    #[Test]
    public function it_splits_a_dotted_path_into_segments(): void
    {
        self::assertSame(['page', 'size'], PathNavigator::segments('page.size', '.'));
    }

    #[Test]
    public function it_returns_no_segments_for_an_empty_path(): void
    {
        self::assertSame([], PathNavigator::segments('', '/'));
    }

    #[Test]
    public function it_ignores_empty_segments(): void
    {
        self::assertSame(['a', 'b'], PathNavigator::segments('/a//b/', '/'));
    }

    #[Test]
    public function it_returns_the_head_segment(): void
    {
        self::assertSame('data', PathNavigator::head('/data/attributes', '/'));
    }

    #[Test]
    public function it_returns_an_empty_head_for_an_empty_path(): void
    {
        self::assertSame('', PathNavigator::head('', '/'));
    }

    #[Test]
    public function it_returns_the_tail_segments(): void
    {
        self::assertSame(['attributes', 'title'], PathNavigator::tail('/data/attributes/title', '/'));
    }

    #[Test]
    public function it_counts_depth(): void
    {
        self::assertSame(3, PathNavigator::depth('/data/attributes/title', '/'));
    }

    #[Test]
    public function it_reports_nested_paths(): void
    {
        self::assertTrue(PathNavigator::isNested('/data/attributes', '/'));
        self::assertFalse(PathNavigator::isNested('/data', '/'));
    }
}
