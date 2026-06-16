<?php

declare(strict_types=1);

namespace Psr\Messages\Json;

use Psr\Http\Message\ResponseInterface;
use Psr\Messages\Json\Document\JsonDocument;
use Psr\Messages\Json\Error\JsonErrorDocument;
use Psr\Messages\MediaType;
use Psr\Messages\MediaTypeResponseFactoryInterface;
use Psr\Server\ResponseFactory\ResponseFactory;
use Psr\Server\ResponseFactory\Status;
use Serializer\SerializableInterface;

final readonly class JsonResponseFactory implements MediaTypeResponseFactoryInterface
{
    public function __construct(
        private ResponseFactory $responseFactory,
    ) {
    }

    #[\Override]
    public function mediaType(): MediaType
    {
        return MediaType::JSON;
    }

    /**
     * Builds a response for a plain JSON document.
     *
     * @throws \Throwable
     */
    public function document(JsonDocument $document, Status $status): ResponseInterface
    {
        return $this->respond($document, $status);
    }

    /**
     * @throws \Throwable
     */
    #[\Override]
    public function error(\Throwable $throwable, Status $status): ResponseInterface
    {
        return $this->respond(JsonErrorDocument::fromThrowable($throwable), $status);
    }

    /**
     * @param SerializableInterface<array<string, mixed>> $document
     *
     * @throws \Throwable
     */
    private function respond(SerializableInterface $document, Status $status): ResponseInterface
    {
        return ($this->responseFactory)($status, $document)
            ->withHeader('content-type', $this->mediaType()->value);
    }
}
