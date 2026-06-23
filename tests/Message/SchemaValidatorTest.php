<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Message;

use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Messages\Message\InvalidBodyException;
use Psr\Messages\Message\SchemaValidator;
use Psr\Messages\Tests\Message\Fixtures\StubSchemaFactory;
use Psr\Messages\Tests\Message\Fixtures\StubSchemaValidator;

final class SchemaValidatorTest extends TestCase
{
    #[Test]
    public function it_returns_the_message_unchanged_when_the_body_is_valid(): void
    {
        $validator = new SchemaValidator(new StubSchemaValidator(), new StubSchemaFactory());

        $request = new ServerRequest('POST', 'https://api.example.com/things')
            ->withBody(\Nyholm\Psr7\Stream::create('{"data":{"type":"things"}}'));

        self::assertSame($request, $validator($request));
    }

    #[Test]
    public function it_throws_an_invalid_body_exception_when_the_body_is_invalid(): void
    {
        $errors = [['property' => 'data', 'message' => 'required']];
        $validator = new SchemaValidator(new StubSchemaValidator($errors), new StubSchemaFactory());

        $request = new ServerRequest('POST', 'https://api.example.com/things')
            ->withBody(\Nyholm\Psr7\Stream::create('{}'));

        $this->expectException(InvalidBodyException::class);

        $validator($request);
    }
}
