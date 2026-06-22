<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Unit\Message;

use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Messages\Message\HeadersValidator;
use Psr\Messages\Message\InvalidHeadersException;
use Psr\Messages\Tests\Unit\Message\Fixtures\StubSchemaFactory;
use Psr\Messages\Tests\Unit\Message\Fixtures\StubSchemaValidator;

final class HeadersValidatorTest extends TestCase
{
    #[Test]
    public function it_returns_the_message_unchanged_when_the_headers_are_valid(): void
    {
        $validator = new HeadersValidator(new StubSchemaValidator(), new StubSchemaFactory());

        $request = new ServerRequest('GET', 'https://api.example.com/things')
            ->withHeader('Authorization', 'Bearer token');

        self::assertSame($request, $validator($request));
    }

    #[Test]
    public function it_throws_an_invalid_headers_exception_when_the_headers_are_invalid(): void
    {
        $errors = [['property' => 'authorization', 'message' => 'required']];
        $validator = new HeadersValidator(new StubSchemaValidator($errors), new StubSchemaFactory());

        $request = new ServerRequest('GET', 'https://api.example.com/things');

        $this->expectException(InvalidHeadersException::class);

        $validator($request);
    }
}
