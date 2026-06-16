<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Unit\JsonApi\Query;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Messages\JsonApi\Query\Definition\Filters;

final class FiltersTest extends TestCase
{
    #[Test]
    public function it_parses_filters_keyed_by_field(): void
    {
        $filters = Filters::deserialize(['filter' => ['status' => 'published', 'author' => 'a-1']]);

        self::assertCount(2, $filters->filters);
        self::assertSame('status', $filters->filters[0]->field);
        self::assertSame('published', $filters->filters[0]->value);
    }

    #[Test]
    public function it_is_empty_when_no_filter_is_given(): void
    {
        $filters = Filters::deserialize([]);

        self::assertSame([], $filters->filters);
    }
}
