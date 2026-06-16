<?php

declare(strict_types=1);

namespace Psr\Messages;

use Psr\Http\Message\ResponseInterface;
use Psr\Server\ResponseFactory\Status;

/**
 * Builds responses for a specific media type. Every media type can report
 * itself and render an error response; the concrete document responses (single,
 * collection) are specific to each factory.
 */
interface MediaTypeResponseFactoryInterface
{
    public function mediaType(): MediaType;

    /**
     * Builds an error response from a throwable, rendered for this media type.
     *
     * @throws \Throwable
     */
    public function error(\Throwable $throwable, Status $status): ResponseInterface;
}
