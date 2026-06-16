<?php

declare(strict_types=1);

namespace Psr\Messages\Message;

use Psr\Http\Message\MessageInterface;
use Psr\Messages\Exception\MalformedContentException;

/**
 * Reads a message's headers as a flat map of lowercase name to header line, and
 * as a nested object for JSON Schema validation.
 */
trait HeadersTrait
{
    /**
     * @return array<string, string>
     */
    private function headers(MessageInterface $message): array
    {
        $names = array_keys($message->getHeaders());

        return array_combine(
            array_map(strtolower(...), $names),
            array_map(static fn (string $name): string => $message->getHeaderLine($name), $names),
        );
    }

    /**
     * @throws MalformedContentException
     */
    private function headersAsObject(MessageInterface $message): object
    {
        return Json::objectFromArray($this->headers($message));
    }
}
