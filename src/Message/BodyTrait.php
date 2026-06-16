<?php

declare(strict_types=1);

namespace Psr\Messages\Message;

use Psr\Http\Message\MessageInterface;
use Psr\Messages\Exception\MalformedContentException;

/**
 * Reads and decodes a PSR-7 message body. The array form feeds schema
 * resolution and deserialization; the object form feeds JSON Schema validators.
 */
trait BodyTrait
{
    /**
     * @return array<array-key, mixed>
     *
     * @throws MalformedContentException
     */
    private function decodeBody(MessageInterface $message): array
    {
        return Json::toArray($this->readBody($message));
    }

    /**
     * @throws MalformedContentException
     */
    private function decodeBodyAsObject(MessageInterface $message): object
    {
        return Json::toObject($this->readBody($message));
    }

    private function readBody(MessageInterface $message): string
    {
        $stream = $message->getBody();
        $body = (string) $stream;

        if ($stream->isSeekable()) {
            $stream->rewind();
        }

        return $body;
    }
}
