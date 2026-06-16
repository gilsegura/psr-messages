<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Unit\Message;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Messages\Error\Definition\ErrorCode;
use Psr\Messages\Error\Definition\SourceType;
use Psr\Messages\Message\InvalidBodyException;
use Psr\Messages\Message\InvalidHeadersException;
use Psr\Messages\Message\InvalidQueryException;

final class ValidationExceptionTest extends TestCase
{
    #[Test]
    public function invalid_body_carries_errors_and_a_pointer_source(): void
    {
        $exception = InvalidBodyException::withErrors([['pointer' => '/data/attributes/title']]);

        self::assertSame(ErrorCode::MALFORMED_CONTENT, $exception->errorCode());
        self::assertCount(1, $exception->errors());

        $source = $exception->sourceFor(['pointer' => '/data/attributes/title']);
        self::assertSame(SourceType::POINTER, $source->type);
        self::assertSame('/data/attributes/title', $source->path);
    }

    #[Test]
    public function invalid_query_carries_a_parameter_source(): void
    {
        $exception = InvalidQueryException::withErrors([['property' => 'page']]);

        self::assertSame(ErrorCode::MALFORMED_QUERY_PARAM, $exception->errorCode());

        $source = $exception->sourceFor(['property' => 'page']);
        self::assertSame(SourceType::PARAMETER, $source->type);
    }

    #[Test]
    public function invalid_headers_carries_a_header_source(): void
    {
        $exception = InvalidHeadersException::withErrors([['property' => 'authorization']]);

        self::assertSame(ErrorCode::MALFORMED_HEADER, $exception->errorCode());

        $source = $exception->sourceFor(['property' => 'authorization']);
        self::assertSame(SourceType::HEADER, $source->type);
    }

    #[Test]
    public function it_derives_a_title_from_the_class_name(): void
    {
        $exception = InvalidBodyException::withErrors([]);

        self::assertSame('Invalid body', $exception->title());
    }

    #[Test]
    public function its_detail_is_the_message(): void
    {
        $exception = InvalidBodyException::withErrors([]);

        self::assertSame('The request body is not valid.', $exception->detail());
    }
}
