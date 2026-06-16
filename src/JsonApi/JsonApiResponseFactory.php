<?php

declare(strict_types=1);

namespace Psr\Messages\JsonApi;

use Psr\Http\Message\ResponseInterface;
use Psr\Messages\JsonApi\Document\ResourceCollectionDocument;
use Psr\Messages\JsonApi\Document\SingleResourceDocument;
use Psr\Messages\JsonApi\Error\JsonApiErrorDocument;
use Psr\Messages\MediaType;
use Psr\Messages\MediaTypeResponseFactoryInterface;
use Psr\Server\ResponseFactory\ResponseFactory;
use Psr\Server\ResponseFactory\Status;
use Serializer\SerializableInterface;

final readonly class JsonApiResponseFactory implements MediaTypeResponseFactoryInterface
{
    public function __construct(
        private ResponseFactory $responseFactory,
    ) {
    }

    #[\Override]
    public function mediaType(): MediaType
    {
        return MediaType::JSON_API;
    }

    /**
     * Builds a response for a single resource document.
     *
     * @throws \Throwable
     */
    public function single(SingleResourceDocument $document, Status $status): ResponseInterface
    {
        return $this->respond($document, $status);
    }

    /**
     * Builds a response for a resource collection document.
     *
     * @throws \Throwable
     */
    public function collection(ResourceCollectionDocument $document, Status $status): ResponseInterface
    {
        return $this->respond($document, $status);
    }

    /**
     * @throws \Throwable
     */
    #[\Override]
    public function error(\Throwable $throwable, Status $status): ResponseInterface
    {
        return $this->respond(JsonApiErrorDocument::fromThrowable($throwable), $status);
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
