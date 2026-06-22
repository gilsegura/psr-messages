<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Unit\JsonApi\Query;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Messages\JsonApi\Query\Definition\SortDirection;
use Psr\Messages\Tests\Unit\JsonApi\Query\Fixtures\StubSort;

final class SortTest extends TestCase
{
    #[Test]
    public function it_parses_ascending_and_descending_fields(): void
    {
        $sort = StubSort::deserialize(['sort' => '-created,name']);

        self::assertSame('created', $sort->fields[0]->field);
        self::assertSame(SortDirection::DESC, $sort->fields[0]->direction);
        self::assertSame('name', $sort->fields[1]->field);
        self::assertSame(SortDirection::ASC, $sort->fields[1]->direction);
    }

    #[Test]
    public function it_is_empty_when_no_sort_is_given(): void
    {
        self::assertTrue(StubSort::deserialize([])->isEmpty());
    }

    #[Test]
    public function it_answers_whether_it_sorts_by_a_field_and_in_which_direction(): void
    {
        $sort = StubSort::deserialize(['sort' => '-created,name']);

        self::assertTrue($sort->has('created'));
        self::assertFalse($sort->has('updated'));
        self::assertSame(SortDirection::DESC, $sort->directionFor('created'));
        self::assertSame(SortDirection::ASC, $sort->directionFor('name'));
        self::assertNull($sort->directionFor('updated'));
    }
}
