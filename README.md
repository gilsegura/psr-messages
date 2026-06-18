# PSR MESSAGES
[![tests](https://github.com/gilsegura/psr-messages/actions/workflows/tests.yaml/badge.svg)](https://github.com/gilsegura/psr-messages/actions/workflows/tests.yaml)
[![codecov](https://codecov.io/github/gilsegura/psr-messages/graph/badge.svg)](https://codecov.io/github/gilsegura/psr-messages)
[![static analysis](https://github.com/gilsegura/psr-messages/actions/workflows/static-analysis.yaml/badge.svg)](https://github.com/gilsegura/psr-messages/actions/workflows/static-analysis.yaml)
[![coding standards](https://github.com/gilsegura/psr-messages/actions/workflows/coding-standards.yaml/badge.svg)](https://github.com/gilsegura/psr-messages/actions/workflows/coding-standards.yaml)

Framework-agnostic toolkit for parsing, validating and producing HTTP messages
on top of PSR-7 and PSR-15, with first-class support for JSON and JSON:API. It
turns a raw request body, headers and query parameters into typed schema objects,
validates them against JSON Schema, and builds JSON / JSON:API responses and
error documents. The `gilsegura/psr-messages-bundle` package wires these
middlewares into Symfony.

## Installation

```bash
composer require gilsegura/psr-messages
```

## Media types

`MediaType` is the enum the toolkit is organised around: `JSON`
(`application/json`) and `JSON_API` (`application/vnd.api+json`). It resolves the
media type from a header line, and `SupportsMediaTypeTrait` /
`MediaTypeParserInterface` / `MediaTypeResponseFactoryInterface` let parsers and
response factories declare which media type they handle.

## Schemas

A schema turns raw input into a typed, validated object:

- **`SchemaInterface`** — a serializable type with a static `supports(array $data)`
  guard, so the right schema is chosen for the incoming data and deserialized
  into a typed object.
- **`SchemaResolverInterface` / `SchemaResolver`** — pick the schema that
  supports a given payload, raising `UnresolvedSchemaException` when none match.

## Messages

`Psr\Messages\Message` validates and exposes the three parts of a request:

- **Body** — `BodyTrait`, `SchemaValidator` and `InvalidBodyException` parse and
  validate a request body against a JSON Schema.
- **Headers** — `HeadersTrait`, `HeadersValidator` and `InvalidHeadersException`.
- **Query** — `QueryTrait`, `QueryParamsValidator`, `InvalidQueryException`.

Each validator checks the raw input against a schema and surfaces a typed
exception on failure.

## Middlewares (PSR-15)

Three middlewares resolve and type the parts of a request, storing the result so
handlers downstream can read typed objects instead of raw arrays:

- **`ParsedBodyMiddleware`** — parses the body with the endpoint's parser and
  stores the resolved schema as the parsed body.
- **`ParseHeadersMiddleware`** — resolves the headers schema and stores the typed
  object as a request attribute, keyed by its class.
- **`ParseQueryParamsMiddleware`** — same for query parameters; a request with no
  query parameters passes through untouched.

## JSON

`Psr\Messages\Json` handles plain `application/json`:

- **`JsonParser`** — parses a JSON body.
- **`JsonResponseFactory`** — builds a JSON response.
- **`JsonDocument`** and **`JsonErrorDocument`** — the response and error shapes.

## JSON:API

`Psr\Messages\JsonApi` implements `application/vnd.api+json`:

- **`JsonApiParser`** — parses a JSON:API request.
- **`JsonApiResponseFactory`** — builds JSON:API responses.
- **`SingleResourceDocument` / `ResourceCollectionDocument`** — single and
  collection resource documents; `JsonApiErrorDocument` for errors.
- **`JsonApiQuerySchema`** — parses JSON:API query parameters (filtering, sorting,
  pagination) into a typed query.
- The `Definition` namespaces describe resources, links and meta.

## Links and errors

`Psr\Messages\Link` models hypermedia links (`Href`, `LinkType`, `LinkContext`),
and `Psr\Messages\Error` provides the error definitions both document formats
render.

## License
MIT. See [LICENSE](LICENSE).