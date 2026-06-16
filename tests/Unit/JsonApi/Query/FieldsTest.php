<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Unit\JsonApi\Query;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Messages\Exception\UnexpectedStateException;
use Psr\Messages\JsonApi\Query\Definition\Fields;

final class FieldsTest extends TestCase
{
    #[Test]
    public function it_parses_sparse_fieldsets_per_type(): void
    {
        $fields = Fields::deserialize(['fields' => ['articles' => 'title,body', 'people' => 'name']]);

        self::assertCount(2, $fields->fields);
        self::assertSame('articles', $fields->fields[0]->type);
        self::assertSame(['title', 'body'], $fields->fields[0]->fields);
        self::assertSame('people', $fields->fields[1]->type);
        self::assertSame(['name'], $fields->fields[1]->fields);
    }

    #[Test]
    public function it_is_empty_when_no_fields_are_given(): void
    {
        $fields = Fields::deserialize([]);

        self::assertSame([], $fields->fields);
    }

    #[Test]
    public function it_throws_when_fields_is_not_an_array(): void
    {
        $this->expectException(UnexpectedStateException::class);

        Fields::deserialize(['fields' => 'nope']);
    }
}
