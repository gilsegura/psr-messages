<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\JsonApi\Query;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Messages\JsonApi\Query\Definition\Path;

final class PathTest extends TestCase
{
    #[Test]
    public function a_plain_path_has_a_single_segment(): void
    {
        $path = new Path('author');

        self::assertSame(['author'], $path->segments());
        self::assertSame('author', $path->head());
        self::assertSame([], $path->tail());
        self::assertFalse($path->isNested());
    }

    #[Test]
    public function a_nested_path_splits_on_the_dot(): void
    {
        $path = new Path('comments.author');

        self::assertSame(['comments', 'author'], $path->segments());
        self::assertSame('comments', $path->head());
        self::assertSame(['author'], $path->tail());
        self::assertTrue($path->isNested());
    }
}
