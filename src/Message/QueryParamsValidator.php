<?php

declare(strict_types=1);

namespace Psr\Messages\Message;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Validator\Exception\ValidationExceptionInterface;
use Psr\Validator\MessageValidatorInterface;
use Psr\Validator\SchemaFactoryInterface;
use Psr\Validator\SchemaValidatorInterface;

/**
 * Validates a request's query parameters against a JSON Schema. Query params
 * arrive as a nested array of strings, so they are converted to a nested object
 * before validation. Leaves the message untouched; throws on validation errors.
 */
final readonly class QueryParamsValidator implements MessageValidatorInterface
{
    use QueryTrait;

    public function __construct(
        private SchemaValidatorInterface $validator,
        private SchemaFactoryInterface $schemaFactory,
    ) {
    }

    /**
     * @throws ValidationExceptionInterface
     */
    #[\Override]
    public function __invoke(MessageInterface $message): MessageInterface
    {
        if (!$message instanceof ServerRequestInterface) {
            return $message;
        }

        $errors = ($this->validator)(
            $this->queryParamsAsObject($message),
            ($this->schemaFactory)(),
        );

        if ([] !== $errors) {
            throw InvalidQueryException::withErrors($errors);
        }

        return $message;
    }
}
