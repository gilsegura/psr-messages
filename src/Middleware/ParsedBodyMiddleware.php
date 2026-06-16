<?php

declare(strict_types=1);

namespace Psr\Messages\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Messages\Exception\UnsupportedMediaTypeException;
use Psr\Messages\MediaType;
use Psr\Messages\MediaTypeParserInterface;

/**
 * Parses a request body with the endpoint's parser and stores the resolved
 * schema in the parsed body. The pipeline of a controller accepts a single
 * content type, so there is one parser: supports() guards that the incoming
 * content type is the one this parser handles (415 otherwise), rather than
 * choosing among several. An empty body passes through untouched.
 */
final readonly class ParsedBodyMiddleware implements MiddlewareInterface
{
    public function __construct(
        private MediaTypeParserInterface $parser,
    ) {
    }

    /**
     * @throws UnsupportedMediaTypeException
     */
    #[\Override]
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
    ): ResponseInterface {
        if (0 === $request->getBody()->getSize()) {
            return $handler->handle($request);
        }

        $mediaType = MediaType::fromHeaderLine($request->getHeaderLine('content-type'));

        if (!$this->parser->supports($mediaType)) {
            throw UnsupportedMediaTypeException::unsupported($mediaType->value);
        }

        return $handler->handle(
            $this->parser->parse($request),
        );
    }
}
