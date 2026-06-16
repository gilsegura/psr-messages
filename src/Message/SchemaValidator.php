<?php

declare(strict_types=1);

namespace Psr\Messages\Message;

use Psr\Http\Message\MessageInterface;
use Psr\Validator\Exception\ValidationExceptionInterface;
use Psr\Validator\MessageValidatorInterface;
use Psr\Validator\SchemaFactoryInterface;
use Psr\Validator\SchemaValidatorInterface;

/**
 * Validates a message body against a JSON Schema, decoding the body with the
 * shared decoder. Leaves the message untouched; throws on validation errors.
 */
final readonly class SchemaValidator implements MessageValidatorInterface
{
    use BodyTrait;

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
        $errors = ($this->validator)(
            $this->decodeBodyAsObject($message),
            ($this->schemaFactory)(),
        );

        if ([] !== $errors) {
            throw InvalidBodyException::withErrors($errors);
        }

        return $message;
    }
}
