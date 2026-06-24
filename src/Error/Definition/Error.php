<?php

declare(strict_types=1);

namespace Psr\Messages\Error\Definition;

use Psr\Messages\Exception\MissingErrorSourceException;
use Psr\Messages\JsonApi\Definition\HasMetaInterface;
use Psr\Messages\Link\Definition\HasLinksInterface;
use Psr\Messages\Link\Definition\Link;
use Psr\Messages\Support\ClassName;

/**
 * A concrete, media-type-agnostic error. Implements ErrorInterface, plus the
 * optional source, links and meta interfaces, so it can carry whatever parts an
 * error needs. Build it directly, from a throwable, or enrich it with with*().
 */
final readonly class Error implements ErrorInterface, HasErrorSourceInterface, HasLinksInterface, HasMetaInterface
{
    /**
     * @param Link[]               $links
     * @param array<string, mixed> $meta
     */
    public function __construct(
        private ErrorCodeInterface $errorCode,
        private string $title,
        private string $detail,
        private ?Source $source = null,
        public array $links = [],
        public array $meta = [],
    ) {
    }

    /**
     * Normalizes any throwable into a single error. A throwable that is already
     * an ErrorInterface keeps its code, title and detail (and its source, links
     * and meta when present); any other is described generically from its class
     * name, with the message as detail.
     */
    public static function fromThrowable(\Throwable $throwable): self
    {
        if (!$throwable instanceof ErrorInterface) {
            return new self(
                ErrorCode::INTERNAL_ERROR,
                ClassName::toTitle($throwable::class),
                $throwable->getMessage(),
            );
        }

        return new self(
            $throwable->errorCode(),
            $throwable->title(),
            $throwable->detail(),
            $throwable instanceof HasErrorSourceInterface ? $throwable->source() : null,
            $throwable instanceof HasLinksInterface ? $throwable->links : [],
            $throwable instanceof HasMetaInterface ? $throwable->meta : [],
        );
    }

    public function withDetail(string $detail): self
    {
        return new self($this->errorCode, $this->title, $detail, $this->source, $this->links, $this->meta);
    }

    #[\Override]
    public function withSource(Source $source): static
    {
        return new self($this->errorCode, $this->title, $this->detail, $source, $this->links, $this->meta);
    }

    #[\Override]
    public function withLinks(Link ...$links): static
    {
        return new self($this->errorCode, $this->title, $this->detail, $this->source, $links, $this->meta);
    }

    #[\Override]
    public function withMeta(array $meta): static
    {
        return new self($this->errorCode, $this->title, $this->detail, $this->source, $this->links, $meta);
    }

    #[\Override]
    public function errorCode(): ErrorCodeInterface
    {
        return $this->errorCode;
    }

    #[\Override]
    public function title(): string
    {
        return $this->title;
    }

    #[\Override]
    public function detail(): string
    {
        return $this->detail;
    }

    #[\Override]
    public function source(): Source
    {
        return $this->source ?? throw MissingErrorSourceException::create();
    }

    public function hasSource(): bool
    {
        return $this->source instanceof Source;
    }
}
