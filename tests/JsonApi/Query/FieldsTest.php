<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\JsonApi\Query;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Messages\Tests\JsonApi\Query\Fixtures\StubFields;
use Psr\Messages\Tests\JsonApi\Query\Fixtures\StubType;

final class FieldsTest extends TestCase
{
    #[Test]
    public function it_parses_a_fieldset_per_type(): void
    {
        $fields = StubFields::deserialize(['fields' => ['articles' => 'title,body']]);

        $field = $fields->forType(StubType::ARTICLE);

        self::assertNotNull($field);
        self::assertSame(['title', 'body'], $field->fields);
        self::assertTrue($field->has('title'));
        self::assertFalse($field->has('summary'));
    }

    #[Test]
    public function it_returns_null_for_an_unconstrained_type(): void
    {
        $fields = StubFields::deserialize(['fields' => ['articles' => 'title']]);

        self::assertNull($fields->forType(StubType::PERSON));
        self::assertFalse($fields->has(StubType::PERSON));
    }

    #[Test]
    public function it_applies_the_fieldset_keeping_only_requested_attributes(): void
    {
        $fields = StubFields::deserialize(['fields' => ['articles' => 'title']]);

        $kept = $fields->apply(StubType::ARTICLE, ['title' => 'Hi', 'body' => 'X']);

        self::assertSame(['title' => 'Hi'], $kept);
    }

    #[Test]
    public function it_leaves_attributes_untouched_for_an_unconstrained_type(): void
    {
        $fields = StubFields::deserialize([]);

        $attributes = ['title' => 'Hi', 'body' => 'X'];

        self::assertSame($attributes, $fields->apply(StubType::ARTICLE, $attributes));
    }
}
