<?php

declare(strict_types=1);

namespace Psr\Messages\Error;

use Psr\Messages\Error\Definition\Error;
use Psr\Messages\Exception\UnexpectedStateException;
use Psr\Messages\Exception\UnsupportedDeserializationException;
use Serializer\SerializableInterface;

/**
 * A collection of errors, rendered to a media type by its subclass. The
 * expansion of a throwable into errors is shared here; each subclass only
 * decides how the errors are serialized. Output only.
 *
 * @implements SerializableInterface<array<string, mixed>>
 *
 * @phpstan-consistent-constructor
 */
abstract readonly class ErrorDocument implements SerializableInterface
{
    /** @var Error[] */
    protected array $errors;

    public function __construct(Error ...$errors)
    {
        $this->errors = $errors;
    }

    /**
     * Normalizes a throwable into an error document. A sourced validation
     * exception expands into one error per raw validation error, each pointing
     * to its own path; any other throwable becomes a single error.
     */
    public static function fromThrowable(\Throwable $throwable): static
    {
        if (!$throwable instanceof SourcedValidationExceptionInterface) {
            return new static(Error::fromThrowable($throwable));
        }

        $base = Error::fromThrowable($throwable);

        return new static(...array_map(
            static fn (array $error): Error => $base
                ->withDetail(self::message($error))
                ->withSource($throwable->sourceFor($error)),
            $throwable->errors(),
        ));
    }

    /**
     * Reads the message from a raw validation error. The validator always
     * provides it, so a missing or non-string value is an impossible state.
     *
     * @param array<string, mixed> $error
     */
    private static function message(array $error): string
    {
        if (!isset($error['message']) || !\is_string($error['message'])) {
            throw UnexpectedStateException::reason('the validation error has no string "message".');
        }

        return $error['message'];
    }

    /**
     * @throws UnsupportedDeserializationException always; an error document is output only
     */
    #[\Override]
    public static function deserialize(array $attributes): static
    {
        throw UnsupportedDeserializationException::for('An error document');
    }

    /**
     * @return array<string, mixed>
     */
    #[\Override]
    abstract public function serialize(): array;
}
