<?php

declare(strict_types=1);

namespace Psr\Messages\Error;

use Psr\Messages\Error\Definition\ErrorInterface;
use Psr\Messages\Error\Definition\Source;
use Psr\Validator\Exception\ValidationExceptionInterface;

/**
 * A validation exception that is itself an error (code, title, detail) and
 * carries one or more raw validation errors. It is expanded into one error per
 * raw error, each with its own detail and the source the exception builds for
 * the part of the request it came from.
 */
interface SourcedValidationExceptionInterface extends ValidationExceptionInterface, ErrorInterface
{
    /**
     * @return array<string, mixed>[] the raw validation errors
     */
    public function errors(): array;

    /**
     * Builds the source for a single raw validation error, according to the
     * part of the request this exception came from.
     *
     * @param array<string, mixed> $error
     */
    public function sourceFor(array $error): Source;
}
