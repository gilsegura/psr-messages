<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\JsonApi\Document;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Messages\JsonApi\Document\Definition\ResourceIdentifier;
use Psr\Messages\JsonApi\Document\Definition\ToManyRelationship;
use Psr\Messages\JsonApi\Document\Definition\ToOneRelationship;
use Psr\Messages\Tests\JsonApi\Document\Fixtures\StubType;

final class RelationshipTest extends TestCase
{
    #[Test]
    public function to_one_serializes_a_single_identifier(): void
    {
        $relationship = new ToOneRelationship(new ResourceIdentifier(StubType::PERSON, 'p-1'));

        self::assertSame(['data' => ['type' => 'people', 'id' => 'p-1']], $relationship->serialize());
    }

    #[Test]
    public function to_one_serializes_null_when_empty(): void
    {
        $relationship = new ToOneRelationship();

        self::assertSame(['data' => null], $relationship->serialize());
    }

    #[Test]
    public function to_many_serializes_a_list_of_identifiers(): void
    {
        $relationship = new ToManyRelationship([
            new ResourceIdentifier(StubType::ARTICLE, '1'),
            new ResourceIdentifier(StubType::ARTICLE, '2'),
        ]);

        self::assertSame([
            'data' => [
                ['type' => 'articles', 'id' => '1'],
                ['type' => 'articles', 'id' => '2'],
            ],
        ], $relationship->serialize());
    }

    #[Test]
    public function to_many_serializes_an_empty_list(): void
    {
        $relationship = new ToManyRelationship();

        self::assertSame(['data' => []], $relationship->serialize());
    }
}
