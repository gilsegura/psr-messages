<?php

declare(strict_types=1);

namespace Psr\Messages\Message;

use Psr\Messages\Error\Definition\DescribesErrorTrait;
use Psr\Messages\Error\Definition\ErrorCode;
use Psr\Messages\Error\Definition\ErrorCodeInterface;
use Psr\Messages\Error\Definition\Source;
use Psr\Messages\Error\SourcedValidationExceptionInterface;

final class InvalidHeadersException extends \RuntimeException implements SourcedValidationExceptionInterface
{
    use DescribesErrorTrait;

    /** @var array<string, mixed>[] */
    private array $errors;

    /**
     * @param array<string, mixed>[] $errors
     */
    public static function withErrors(array $errors): self
    {
        $exception = new self('The request headers are not valid.');
        $exception->errors = $errors;

        return $exception;
    }

    /**
     * @return array<string, mixed>[]
     */
    #[\Override]
    public function errors(): array
    {
        return $this->errors;
    }

    #[\Override]
    public function errorCode(): ErrorCodeInterface
    {
        return ErrorCode::MALFORMED_HEADER;
    }

    /**
     * @param array<string, mixed> $error
     */
    #[\Override]
    public function sourceFor(array $error): Source
    {
        return Source::forHeader($error);
    }
}
