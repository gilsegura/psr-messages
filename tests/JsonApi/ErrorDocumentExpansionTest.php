<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\JsonApi;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Messages\JsonApi\Error\JsonApiErrorDocument;
use Psr\Messages\Message\InvalidBodyException;

final class ErrorDocumentExpansionTest extends TestCase
{
    #[Test]
    public function it_expands_each_validation_error_into_an_error_with_a_source(): void
    {
        $exception = InvalidBodyException::withErrors([
            ['pointer' => '/data/attributes/title', 'message' => 'The property title is required'],
            ['pointer' => '/data/attributes/body', 'message' => 'The property body is required'],
        ]);

        $document = JsonApiErrorDocument::fromThrowable($exception);
        $serialized = $document->serialize();

        self::assertCount(2, $serialized['errors']);
        self::assertSame('malformed_content', $serialized['errors'][0]['code']);
        self::assertSame(
            ['pointer' => '/data/attributes/title'],
            $serialized['errors'][0]['source'],
        );
        self::assertSame(
            ['pointer' => '/data/attributes/body'],
            $serialized['errors'][1]['source'],
        );
    }

    #[Test]
    public function it_renders_a_single_internal_error_for_a_generic_throwable(): void
    {
        $document = JsonApiErrorDocument::fromThrowable(new \RuntimeException('Boom'));
        $serialized = $document->serialize();

        self::assertCount(1, $serialized['errors']);
        self::assertSame('internal_error', $serialized['errors'][0]['code']);
        self::assertSame('Boom', $serialized['errors'][0]['detail']);
        self::assertArrayNotHasKey('source', $serialized['errors'][0]);
    }
}
