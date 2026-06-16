<?php

declare(strict_types=1);

namespace Psr\Messages\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Messages\Message\QueryTrait;
use Psr\Messages\Schema\SchemaResolverInterface;

/**
 * Resolves the query schema that applies to a request's query parameters and
 * stores the typed schema object as a request attribute, keyed by its class, so
 * handlers can retrieve it. If there are no query parameters, the request
 * passes through untouched.
 */
final readonly class ParseQueryParamsMiddleware implements MiddlewareInterface
{
    use QueryTrait;

    public function __construct(
        private SchemaResolverInterface $resolver,
    ) {
    }

    #[\Override]
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $queryParams = $this->queryParams($request);

        if ([] === $queryParams) {
            return $handler->handle($request);
        }

        $schema = $this->resolver->resolve($queryParams);

        return $handler->handle(
            $request->withAttribute($schema::class, $schema),
        );
    }
}
