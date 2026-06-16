<?php

declare(strict_types=1);

namespace Psr\Messages\Schema\Exception;

final class UnresolvedSchemaException extends \LogicException implements SchemaExceptionInterface
{
    /**
     * @param array<array-key, mixed> $data
     */
    public static function forData(array $data): self
    {
        return new self(\sprintf(
            'No schema supports the given data with keys: %s.',
            implode(', ', array_map(strval(...), array_keys($data))),
        ));
    }
}
