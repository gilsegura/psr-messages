<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\JsonApi\Include;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Messages\Tests\JsonApi\Include\Fixtures\StubInclude;
use Psr\Messages\Tests\JsonApi\Query\Fixtures\StubIncludes;
use Psr\Messages\Tests\JsonApi\Query\Fixtures\StubRelationship;

final class IncludeSelectionTest extends TestCase
{
    #[Test]
    public function it_selects_only_the_requested_includes(): void
    {
        $requested = StubIncludes::deserialize(['include' => 'tags']);
        $available = [new StubInclude(StubRelationship::TAGS), new StubInclude(StubRelationship::AUTHOR)];

        $selected = $requested->select(...$available);

        self::assertCount(1, $selected);
        self::assertSame(StubRelationship::TAGS, $selected[0]->name());
    }

    #[Test]
    public function it_selects_nothing_when_no_include_was_requested(): void
    {
        $requested = StubIncludes::deserialize([]);
        $available = [new StubInclude(StubRelationship::TAGS), new StubInclude(StubRelationship::AUTHOR)];

        self::assertSame([], $requested->select(...$available));
    }

    #[Test]
    public function it_matches_a_nested_include_by_its_top_level_name(): void
    {
        $requested = StubIncludes::deserialize(['include' => 'comments.author']);
        $available = [new StubInclude(StubRelationship::COMMENTS), new StubInclude(StubRelationship::TAGS)];

        $selected = $requested->select(...$available);

        self::assertCount(1, $selected);
        self::assertSame(StubRelationship::COMMENTS, $selected[0]->name());
    }
}
