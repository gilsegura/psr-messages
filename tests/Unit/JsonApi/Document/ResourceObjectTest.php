<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Unit\JsonApi\Document;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Messages\JsonApi\Document\Definition\ResourceIdentifier;
use Psr\Messages\JsonApi\Document\Definition\ResourceObject;
use Psr\Messages\JsonApi\Document\Definition\ToManyRelationship;
use Psr\Messages\JsonApi\Document\Definition\ToOneRelationship;
use Psr\Messages\Link\Definition\Href;
use Psr\Messages\Link\Definition\Link;
use Psr\Messages\Link\Definition\LinkType;
use Psr\Messages\Tests\Unit\JsonApi\Document\Fixtures\StubAttributes;
use Psr\Messages\Tests\Unit\JsonApi\Document\Fixtures\StubRelationship;
use Psr\Messages\Tests\Unit\JsonApi\Document\Fixtures\StubType;

final class ResourceObjectTest extends TestCase
{
    #[Test]
    public function it_serializes_type_id_and_attributes(): void
    {
        $resource = new ResourceObject(StubType::ARTICLE, '1', new StubAttributes(['title' => 'Hello']));

        self::assertSame([
            'type' => 'articles',
            'id' => '1',
            'attributes' => ['title' => 'Hello'],
        ], $resource->serialize());
    }

    #[Test]
    public function it_serializes_relationships_links_and_meta(): void
    {
        $resource = new ResourceObject(
            StubType::ARTICLE,
            '1',
            new StubAttributes(['title' => 'Hello']),
            ['author' => new ToOneRelationship(new ResourceIdentifier(StubType::PERSON, 'p-1'))],
            ['tags' => new ToManyRelationship([new ResourceIdentifier(StubType::ARTICLE, 't-1')])],
            [new Link(LinkType::SELF, new Href('https://api.example.com/articles/1'))],
            ['views' => 10],
        );

        $serialized = $resource->serialize();

        self::assertSame(
            ['data' => ['type' => 'people', 'id' => 'p-1']],
            $serialized['relationships']['author'],
        );
        self::assertSame(
            ['data' => [['type' => 'articles', 'id' => 't-1']]],
            $serialized['relationships']['tags'],
        );
        self::assertSame('https://api.example.com/articles/1', $serialized['links']['self']);
        self::assertSame(['views' => 10], $serialized['meta']);
    }

    #[Test]
    public function it_omits_empty_relationships_links_and_meta(): void
    {
        $resource = new ResourceObject(StubType::ARTICLE, '1', new StubAttributes([]));

        $serialized = $resource->serialize();

        self::assertArrayNotHasKey('relationships', $serialized);
        self::assertArrayNotHasKey('links', $serialized);
        self::assertArrayNotHasKey('meta', $serialized);
    }

    #[Test]
    public function it_builds_relationships_one_at_a_time_with_with_methods(): void
    {
        $resource = new ResourceObject(StubType::ARTICLE, '1', new StubAttributes(['title' => 'Hi']))
            ->withOneRelationship(StubRelationship::AUTHOR, new ToOneRelationship(new ResourceIdentifier(StubType::PERSON, 'p-1')))
            ->withManyRelationship(StubRelationship::TAGS, new ToManyRelationship([new ResourceIdentifier(StubType::ARTICLE, 't-1')]));

        $serialized = $resource->serialize();

        self::assertSame(
            ['data' => ['type' => 'people', 'id' => 'p-1']],
            $serialized['relationships']['author'],
        );
        self::assertSame(
            ['data' => [['type' => 'articles', 'id' => 't-1']]],
            $serialized['relationships']['tags'],
        );
    }

    #[Test]
    public function it_adds_links_and_meta_with_with_methods(): void
    {
        $resource = new ResourceObject(StubType::ARTICLE, '1', new StubAttributes([]))
            ->withLinks(new Link(LinkType::SELF, new Href('https://api.example.com/articles/1')))
            ->withMeta(['views' => 10]);

        $serialized = $resource->serialize();

        self::assertSame('https://api.example.com/articles/1', $serialized['links']['self']);
        self::assertSame(['views' => 10], $serialized['meta']);
    }
}
