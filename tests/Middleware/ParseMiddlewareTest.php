<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Middleware;

use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Messages\Exception\UnsupportedMediaTypeException;
use Psr\Messages\JsonApi\JsonApiParser;
use Psr\Messages\Middleware\ParsedBodyMiddleware;
use Psr\Messages\Middleware\ParseQueryParamsMiddleware;
use Psr\Messages\Schema\SchemaResolver;
use Psr\Messages\Tests\Middleware\Fixtures\StubQuerySchema;
use Psr\Messages\Tests\Schema\Fixtures\UpperSchema;

final class ParseMiddlewareTest extends TestCase
{
    #[Test]
    public function parsed_body_middleware_leaves_the_typed_schema_in_the_parsed_body(): void
    {
        $middleware = new ParsedBodyMiddleware(new JsonApiParser(new SchemaResolver(UpperSchema::class)));

        $request = new ServerRequest(
            'POST',
            'https://api.example.com/things',
            ['Content-Type' => 'application/vnd.api+json'],
            '{"kind":"upper","value":"hi"}',
        );

        $handler = new CapturingHandler();
        $middleware->process($request, $handler);

        $parsed = $handler->captured()->getParsedBody();
        self::assertInstanceOf(UpperSchema::class, $parsed);
        self::assertSame('HI', $parsed->value);
    }

    #[Test]
    public function parsed_body_middleware_passes_an_empty_body_through_untouched(): void
    {
        $middleware = new ParsedBodyMiddleware(new JsonApiParser(new SchemaResolver(UpperSchema::class)));

        $request = new ServerRequest('GET', 'https://api.example.com/things');

        $handler = new CapturingHandler();
        $middleware->process($request, $handler);

        self::assertNull($handler->captured()->getParsedBody());
    }

    #[Test]
    public function parsed_body_middleware_rejects_an_unsupported_media_type(): void
    {
        $middleware = new ParsedBodyMiddleware(new JsonApiParser(new SchemaResolver(UpperSchema::class)));

        $request = new ServerRequest(
            'POST',
            'https://api.example.com/things',
            ['Content-Type' => 'application/json'],
            '{"kind":"upper","value":"hi"}',
        );

        $this->expectException(UnsupportedMediaTypeException::class);

        $middleware->process($request, new CapturingHandler());
    }

    #[Test]
    public function query_middleware_stores_the_query_schema_as_a_request_attribute(): void
    {
        $middleware = new ParseQueryParamsMiddleware(new SchemaResolver(StubQuerySchema::class));

        $request = new ServerRequest('GET', 'https://api.example.com/posts')
            ->withQueryParams(['sort' => 'title', 'page' => ['number' => '2', 'size' => '5']]);

        $handler = new CapturingHandler();
        $middleware->process($request, $handler);

        $query = $handler->captured()->getAttribute(StubQuerySchema::class);
        self::assertInstanceOf(StubQuerySchema::class, $query);
        self::assertSame(2, $query->page->number);
        self::assertSame(5, $query->page->size);
    }

    #[Test]
    public function query_middleware_passes_through_when_there_are_no_query_params(): void
    {
        $middleware = new ParseQueryParamsMiddleware(new SchemaResolver(StubQuerySchema::class));

        $request = new ServerRequest('GET', 'https://api.example.com/posts');

        $handler = new CapturingHandler();
        $middleware->process($request, $handler);

        self::assertNull($handler->captured()->getAttribute(StubQuerySchema::class));
    }
}

/**
 * A PSR-15 handler that records the request it received, so middleware tests can
 * assert on what was passed down the stack.
 */
final class CapturingHandler implements RequestHandlerInterface
{
    private ?ServerRequestInterface $request = null;

    #[\Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->request = $request;

        return new Response(200);
    }

    public function captured(): ServerRequestInterface
    {
        \assert($this->request instanceof ServerRequestInterface);

        return $this->request;
    }
}
