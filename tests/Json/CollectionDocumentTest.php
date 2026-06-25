<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Json;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Messages\Json\Document\JsonCollectionDocument;
use Psr\Messages\Tests\Json\Fixtures\StubReadModel;

final class CollectionDocumentTest extends TestCase
{
    #[Test]
    public function it_serializes_a_list_of_payloads(): void
    {
        $document = new JsonCollectionDocument(
            new StubReadModel('1', 'Ada'),
            new StubReadModel('2', 'Linus'),
        );

        self::assertSame([
            ['id' => '1', 'name' => 'Ada'],
            ['id' => '2', 'name' => 'Linus'],
        ], $document->serialize());
    }

    #[Test]
    public function it_serializes_an_empty_collection_as_an_empty_list(): void
    {
        self::assertSame([], new JsonCollectionDocument()->serialize());
    }
}
