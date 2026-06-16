<?php

declare(strict_types=1);

namespace Psr\Messages\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Messages\Message\HeadersTrait;
use Psr\Messages\Schema\SchemaResolverInterface;

/**
 * Resolves the headers schema that applies to a request's headers and stores
 * the typed schema object as a request attribute, keyed by its class, so
 * handlers can retrieve it.
 *
 * Headers are extracted as a flat map of lowercase name to header line. If
 * there are none, the request passes through untouched.
 */
final readonly class ParseHeadersMiddleware implements MiddlewareInterface
{
    use HeadersTrait;

    public function __construct(
        private SchemaResolverInterface $resolver,
    ) {
    }

    #[\Override]
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $headers = $this->headers($request);

        if ([] === $headers) {
            return $handler->handle($request);
        }

        $schema = $this->resolver->resolve($headers);

        return $handler->handle(
            $request->withAttribute($schema::class, $schema),
        );
    }
}
