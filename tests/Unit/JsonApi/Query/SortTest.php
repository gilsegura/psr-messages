<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Unit\JsonApi\Query;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Messages\Exception\UnexpectedStateException;
use Psr\Messages\JsonApi\Query\Definition\Sort;
use Psr\Messages\JsonApi\Query\Definition\SortDirection;

final class SortTest extends TestCase
{
    #[Test]
    public function it_parses_ascending_and_descending_fields(): void
    {
        $sort = Sort::deserialize(['sort' => 'title,-created']);

        self::assertCount(2, $sort->fields);
        self::assertSame('title', $sort->fields[0]->field);
        self::assertSame(SortDirection::ASC, $sort->fields[0]->direction);
        self::assertSame('created', $sort->fields[1]->field);
        self::assertSame(SortDirection::DESC, $sort->fields[1]->direction);
    }

    #[Test]
    public function it_is_empty_when_no_sort_is_given(): void
    {
        $sort = Sort::deserialize([]);

        self::assertSame([], $sort->fields);
    }

    #[Test]
    public function it_throws_when_sort_is_an_empty_string(): void
    {
        $this->expectException(UnexpectedStateException::class);

        Sort::deserialize(['sort' => '']);
    }
}
