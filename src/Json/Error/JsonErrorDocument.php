<?php

declare(strict_types=1);

namespace Psr\Messages\Json\Error;

use Psr\Messages\Error\Definition\ErrorInterface;
use Psr\Messages\Error\ErrorDocument;

/**
 * An error document rendered as plain JSON: a top-level "error" code and
 * message from the first error, plus an "errors" array describing each one.
 */
final readonly class JsonErrorDocument extends ErrorDocument
{
    /**
     * @return array<string, mixed>
     */
    #[\Override]
    public function serialize(): array
    {
        $first = $this->errors[0] ?? null;

        return [
            'error' => $first?->errorCode()->value ?? 'error',
            'message' => $first?->detail() ?? '',
            'errors' => array_map($this->item(...), $this->errors),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function item(ErrorInterface $error): array
    {
        return [
            'code' => (string) $error->errorCode()->value,
            'title' => $error->title(),
            'detail' => $error->detail(),
        ];
    }
}
