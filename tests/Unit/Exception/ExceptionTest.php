<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Unit\Exception;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Messages\Exception\InvalidHrefException;
use Psr\Messages\Exception\MalformedContentException;
use Psr\Messages\Exception\MissingErrorSourceException;
use Psr\Messages\Exception\UnexpectedStateException;
use Psr\Messages\Exception\UnsupportedDeserializationException;
use Psr\Messages\Exception\UnsupportedMediaTypeException;
use Psr\Messages\Exception\UnsupportedSerializationException;

final class ExceptionTest extends TestCase
{
    #[Test]
    public function invalid_href_names_the_offending_value(): void
    {
        self::assertSame('"::" is not a valid link href.', InvalidHrefException::forValue('::')->getMessage());
    }

    #[Test]
    public function malformed_content_describes_the_reason(): void
    {
        self::assertSame('Malformed content: bad json', MalformedContentException::malformed('bad json')->getMessage());
    }

    #[Test]
    public function malformed_content_keeps_the_previous_throwable(): void
    {
        $previous = new \RuntimeException('boom');

        self::assertSame($previous, MalformedContentException::fromThrowable($previous)->getPrevious());
    }

    #[Test]
    public function missing_error_source_explains_how_to_guard(): void
    {
        self::assertStringContainsString('hasSource()', MissingErrorSourceException::create()->getMessage());
    }

    #[Test]
    public function unexpected_state_describes_the_reason(): void
    {
        self::assertSame('Unexpected state after validation: missing id', UnexpectedStateException::reason('missing id')->getMessage());
    }

    #[Test]
    public function unsupported_deserialization_names_the_type(): void
    {
        self::assertSame('A document is output only and cannot be deserialized.', UnsupportedDeserializationException::for('A document')->getMessage());
    }

    #[Test]
    public function unsupported_serialization_names_the_type(): void
    {
        self::assertSame('A query is input only and cannot be serialized.', UnsupportedSerializationException::for('A query')->getMessage());
    }

    #[Test]
    public function unsupported_media_type_names_the_type(): void
    {
        self::assertSame('Unsupported media type "text/xml".', UnsupportedMediaTypeException::unsupported('text/xml')->getMessage());
    }
}
