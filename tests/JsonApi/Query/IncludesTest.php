<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\JsonApi\Query;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Messages\Tests\JsonApi\Query\Fixtures\StubIncludes;
use Psr\Messages\Tests\JsonApi\Query\Fixtures\StubRelationship;

final class IncludesTest extends TestCase
{
    #[Test]
    public function it_parses_a_comma_separated_include_list(): void
    {
        $includes = StubIncludes::deserialize(['include' => 'author,comments']);

        self::assertSame(['author', 'comments'], $includes->names());
    }

    #[Test]
    public function it_is_empty_when_no_include_is_given(): void
    {
        $includes = StubIncludes::deserialize([]);

        self::assertTrue($includes->isEmpty());
    }

    #[Test]
    public function it_trims_and_drops_empty_entries(): void
    {
        $includes = StubIncludes::deserialize(['include' => 'author, , comments']);

        self::assertCount(2, $includes->paths);
    }

    #[Test]
    public function it_matches_a_top_level_relationship_within_a_nested_include(): void
    {
        $includes = StubIncludes::deserialize(['include' => 'comments.author']);

        self::assertTrue($includes->has(StubRelationship::COMMENTS));
    }

    #[Test]
    public function it_returns_the_path_requested_for_a_relationship(): void
    {
        $includes = StubIncludes::deserialize(['include' => 'comments.author']);

        $path = $includes->forName(StubRelationship::COMMENTS);

        self::assertNotNull($path);
        self::assertSame(['author'], $path->tail());
        self::assertNull($includes->forName(StubRelationship::AUTHOR));
    }

    #[Test]
    public function it_answers_whether_a_relationship_was_requested(): void
    {
        $includes = StubIncludes::deserialize(['include' => 'author']);

        self::assertTrue($includes->has(StubRelationship::AUTHOR));
        self::assertFalse($includes->has(StubRelationship::COMMENTS));
    }
}
