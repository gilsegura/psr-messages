<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Unit\Json;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Messages\Json\Document\JsonDocument;
use Psr\Messages\Tests\Unit\JsonApi\Document\Fixtures\StubAttributes;

final class JsonDocumentTest extends TestCase
{
    #[Test]
    public function it_serializes_its_payload_directly(): void
    {
        $document = new JsonDocument(new StubAttributes(['access_token' => 'abc', 'expires_in' => 3600]));

        self::assertSame(['access_token' => 'abc', 'expires_in' => 3600], $document->serialize());
    }
}
