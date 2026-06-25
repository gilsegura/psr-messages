<?php

declare(strict_types=1);

namespace Psr\Messages\Link\Definition;

use Psr\Messages\Exception\InvalidHrefException;

/**
 * A link target URL. Built through fromAbsolute() (a full URL with a scheme) or
 * fromRelative() (a reference rooted at "/"), each validating its own shape on
 * construction — links are produced by the application, so an invalid href is a
 * programming error. JSON:API allows both absolute and relative links.
 *
 * Query parameters can be appended with withQueryParams(), which returns a new
 * Href, so callers (e.g. pagination) build the page links from a base href
 * without assembling query strings by hand.
 */
final readonly class Href
{
    private function __construct(
        public string $value,
    ) {
    }

    /**
     * A full URL with a scheme, e.g. "https://api.example.com/articles".
     */
    public static function fromAbsolute(string $value): self
    {
        if (false === filter_var($value, \FILTER_VALIDATE_URL)) {
            throw InvalidHrefException::forValue($value);
        }

        return new self($value);
    }

    /**
     * A relative reference rooted at "/", e.g. "/articles?page[number]=1".
     */
    public static function fromRelative(string $value): self
    {
        if (!str_starts_with($value, '/')) {
            throw InvalidHrefException::forValue($value);
        }

        return new self($value);
    }

    /**
     * Returns a new href with the given query parameters appended, encoded as a
     * standard query string. Existing query parameters on the href are kept.
     *
     * @param array<string, scalar|array<array-key, scalar>> $params
     */
    public function withQueryParams(array $params): self
    {
        if ([] === $params) {
            return $this;
        }

        $separator = str_contains($this->value, '?') ? '&' : '?';
        $query = http_build_query($params, '', '&', \PHP_QUERY_RFC3986);

        return new self($this->value.$separator.$query);
    }
}
