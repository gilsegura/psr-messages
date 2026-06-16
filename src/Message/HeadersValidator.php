<?php

declare(strict_types=1);

namespace Psr\Messages\Message;

use Psr\Http\Message\MessageInterface;
use Psr\Validator\Exception\ValidationExceptionInterface;
use Psr\Validator\MessageValidatorInterface;
use Psr\Validator\SchemaFactoryInterface;
use Psr\Validator\SchemaValidatorInterface;

/**
 * Validates a message's headers against a JSON Schema. Headers are extracted as
 * a flat map of lowercase name to header line and converted to an object. The
 * schema declares rules only for the headers it cares about and does not forbid
 * others, since every request carries many standard headers. Leaves the message
 * untouched; throws on validation errors.
 */
final readonly class HeadersValidator implements MessageValidatorInterface
{
    use HeadersTrait;

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
            $this->headersAsObject($message),
            ($this->schemaFactory)(),
        );

        if ([] !== $errors) {
            throw InvalidHeadersException::withErrors($errors);
        }

        return $message;
    }
}
