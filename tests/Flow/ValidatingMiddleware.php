<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Flow;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Validator\MessageValidatorInterface;

/**
 * Adapts a psr-validator MessageValidatorInterface into a PSR-15 middleware for
 * the flow pipeline: it validates the request and, on success, passes the
 * (unchanged) request down the stack. Validation failures bubble up as
 * exceptions, which the application maps to an error response.
 */
final readonly class ValidatingMiddleware implements MiddlewareInterface
{
    public function __construct(
        private MessageValidatorInterface $validator,
    ) {
    }

    #[\Override]
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $validated = ($this->validator)($request);
        \assert($validated instanceof ServerRequestInterface);

        return $handler->handle($validated);
    }
}
