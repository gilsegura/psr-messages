<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Unit\JsonApi\Query;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Messages\Tests\Unit\JsonApi\Query\Fixtures\StubFilters;

final class FiltersTest extends TestCase
{
    #[Test]
    public function it_parses_filter_field_value_pairs(): void
    {
        $filters = StubFilters::deserialize(['filter' => ['status' => 'active']]);

        self::assertSame('active', $filters->forField('status'));
    }

    #[Test]
    public function it_returns_null_for_an_absent_filter(): void
    {
        self::assertNull(StubFilters::deserialize([])->forField('status'));
    }

    #[Test]
    public function it_is_empty_when_no_filter_is_given(): void
    {
        self::assertTrue(StubFilters::deserialize([])->isEmpty());
    }
}
