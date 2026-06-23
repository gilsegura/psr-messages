<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Json;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Messages\Json\Error\JsonErrorDocument;
use Psr\Messages\JsonApi\Document\Definition\JsonApiVersion;
use Psr\Messages\JsonApi\Error\JsonApiErrorDocument;

final class ErrorDocumentTest extends TestCase
{
    #[Test]
    public function json_api_error_document_serializes_errors_and_jsonapi_member(): void
    {
        $document = JsonApiErrorDocument::fromThrowable(new \RuntimeException('Something broke'));

        $serialized = $document->serialize();

        self::assertArrayHasKey('errors', $serialized);
        self::assertNotEmpty($serialized['errors']);
        self::assertSame('Something broke', $serialized['errors'][0]['detail']);
        self::assertSame(JsonApiVersion::V1_1->value, $serialized['jsonapi']['version']);
    }

    #[Test]
    public function json_error_document_serializes_a_flat_error_shape(): void
    {
        $document = JsonErrorDocument::fromThrowable(new \RuntimeException('Bad thing'));

        $serialized = $document->serialize();

        self::assertSame('internal_error', $serialized['error']);
        self::assertSame('Bad thing', $serialized['message']);
        self::assertNotEmpty($serialized['errors']);
    }
}
