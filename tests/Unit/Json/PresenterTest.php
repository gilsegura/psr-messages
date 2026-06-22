<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Unit\Json;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Messages\Json\Document\JsonCollectionDocument;
use Psr\Messages\Json\Document\JsonDocument;
use Psr\Messages\Tests\Unit\Json\Fixtures\StubJsonPresenter;
use Psr\Messages\Tests\Unit\Json\Fixtures\StubReadModel;

final class PresenterTest extends TestCase
{
    #[Test]
    public function it_presents_a_read_model_as_a_document_payload(): void
    {
        $presenter = new StubJsonPresenter();

        $document = new JsonDocument($presenter->present(new StubReadModel('1', 'Ada')));

        self::assertSame(['id' => '1', 'name' => 'Ada'], $document->serialize());
    }

    #[Test]
    public function its_payloads_build_a_collection_document(): void
    {
        $presenter = new StubJsonPresenter();

        $document = new JsonCollectionDocument([
            $presenter->present(new StubReadModel('1', 'Ada')),
            $presenter->present(new StubReadModel('2', 'Linus')),
        ]);

        self::assertCount(2, $document->serialize());
    }
}
