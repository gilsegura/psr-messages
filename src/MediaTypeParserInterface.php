<?php

declare(strict_types=1);

namespace Psr\Messages;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Messages\Exception\MalformedContentException;

interface MediaTypeParserInterface
{
    public function mediaType(): MediaType;

    public function supports(MediaType $mediaType): bool;

    /**
     * Builds the typed parsed body from the raw request body, according to the
     * media type definition, and returns the request carrying it.
     *
     * @throws MalformedContentException
     */
    public function parse(ServerRequestInterface $request): ServerRequestInterface;
}
