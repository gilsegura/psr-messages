<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Message;

use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Messages\Message\InvalidQueryException;
use Psr\Messages\Message\QueryParamsValidator;
use Psr\Messages\Tests\Message\Fixtures\StubSchemaFactory;
use Psr\Messages\Tests\Message\Fixtures\StubSchemaValidator;

final class QueryParamsValidatorTest extends TestCase
{
    #[Test]
    public function it_returns_the_message_unchanged_when_the_query_is_valid(): void
    {
        $validator = new QueryParamsValidator(new StubSchemaValidator(), new StubSchemaFactory());

        $request = new ServerRequest('GET', 'https://api.example.com/things')
            ->withQueryParams(['page' => ['number' => '1']]);

        self::assertSame($request, $validator($request));
    }

    #[Test]
    public function it_throws_an_invalid_query_exception_when_the_query_is_invalid(): void
    {
        $errors = [['property' => 'page', 'message' => 'invalid']];
        $validator = new QueryParamsValidator(new StubSchemaValidator($errors), new StubSchemaFactory());

        $request = new ServerRequest('GET', 'https://api.example.com/things')
            ->withQueryParams(['page' => 'nope']);

        $this->expectException(InvalidQueryException::class);

        $validator($request);
    }
}
