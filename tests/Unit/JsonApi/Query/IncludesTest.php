<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Unit\JsonApi\Query;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Messages\JsonApi\Query\Definition\Includes;

final class IncludesTest extends TestCase
{
    #[Test]
    public function it_parses_a_comma_separated_include_list(): void
    {
        $includes = Includes::deserialize(['include' => 'author,comments']);

        self::assertCount(2, $includes->paths);
        self::assertSame('author', $includes->paths[0]->value);
        self::assertSame('comments', $includes->paths[1]->value);
    }

    #[Test]
    public function it_is_empty_when_no_include_is_given(): void
    {
        $includes = Includes::deserialize([]);

        self::assertSame([], $includes->paths);
    }

    #[Test]
    public function it_trims_and_drops_empty_entries(): void
    {
        $includes = Includes::deserialize(['include' => 'author, , comments']);

        self::assertCount(2, $includes->paths);
    }
}
