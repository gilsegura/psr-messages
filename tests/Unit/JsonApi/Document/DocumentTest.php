<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Unit\JsonApi\Document;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Messages\JsonApi\Document\Definition\JsonApiVersion;
use Psr\Messages\JsonApi\Document\Definition\ResourceObject;
use Psr\Messages\JsonApi\Document\ResourceCollectionDocument;
use Psr\Messages\JsonApi\Document\SingleResourceDocument;
use Psr\Messages\Link\Definition\Href;
use Psr\Messages\Link\Definition\Link;
use Psr\Messages\Link\Definition\LinkType;
use Psr\Messages\Tests\Unit\JsonApi\Document\Fixtures\StubAttributes;
use Psr\Messages\Tests\Unit\JsonApi\Document\Fixtures\StubType;

final class DocumentTest extends TestCase
{
    #[Test]
    public function single_serializes_data_and_the_jsonapi_member(): void
    {
        $document = new SingleResourceDocument(
            new ResourceObject(StubType::ARTICLE, '1', new StubAttributes(['title' => 'Hi'])),
        );

        $serialized = $document->serialize();

        self::assertSame('articles', $serialized['data']['type']);
        self::assertSame(JsonApiVersion::V1_1->value, $serialized['jsonapi']['version']);
    }

    #[Test]
    public function single_includes_links_meta_and_included(): void
    {
        $document = new SingleResourceDocument(
            new ResourceObject(StubType::ARTICLE, '1', new StubAttributes([])),
        )
            ->withLinks(new Link(LinkType::SELF, new Href('https://api.example.com/articles/1')))
            ->withMeta(['count' => 1])
            ->withIncluded(new ResourceObject(StubType::PERSON, 'p-1', new StubAttributes(['name' => 'Ada'])));

        $serialized = $document->serialize();

        self::assertSame('https://api.example.com/articles/1', $serialized['links']['self']);
        self::assertSame(['count' => 1], $serialized['meta']);
        self::assertSame('people', $serialized['included'][0]['type']);
    }

    #[Test]
    public function collection_serializes_a_list_of_resources(): void
    {
        $document = new ResourceCollectionDocument([
            new ResourceObject(StubType::ARTICLE, '1', new StubAttributes([])),
            new ResourceObject(StubType::ARTICLE, '2', new StubAttributes([])),
        ]);

        $serialized = $document->serialize();

        self::assertCount(2, $serialized['data']);
        self::assertSame('1', $serialized['data'][0]['id']);
        self::assertSame('2', $serialized['data'][1]['id']);
        self::assertSame(JsonApiVersion::V1_1->value, $serialized['jsonapi']['version']);
    }

    #[Test]
    public function collection_includes_pagination_links_and_meta(): void
    {
        $document = new ResourceCollectionDocument([])
            ->withLinks(
                new Link(LinkType::FIRST, new Href('https://api.example.com/articles?page=1')),
                new Link(LinkType::NEXT, new Href('https://api.example.com/articles?page=2')),
            )
            ->withMeta(['total' => 100]);

        $serialized = $document->serialize();

        self::assertSame('https://api.example.com/articles?page=1', $serialized['links']['first']);
        self::assertSame('https://api.example.com/articles?page=2', $serialized['links']['next']);
        self::assertSame(['total' => 100], $serialized['meta']);
    }
}
