<?php

declare(strict_types=1);

namespace Psr\Messages\Message;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Messages\Exception\MalformedContentException;

/**
 * Reads a request's query parameters as an array, and as a nested object for
 * JSON Schema validation.
 */
trait QueryTrait
{
    /**
     * @return array<array-key, mixed>
     */
    private function queryParams(ServerRequestInterface $request): array
    {
        /** @var array<array-key, mixed> $params */
        $params = $request->getQueryParams();

        return $params;
    }

    /**
     * @throws MalformedContentException
     */
    private function queryParamsAsObject(ServerRequestInterface $request): object
    {
        return Json::objectFromArray($this->queryParams($request));
    }
}
