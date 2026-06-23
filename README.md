# PSR MESSAGES
[![tests](https://github.com/gilsegura/psr-messages/actions/workflows/tests.yaml/badge.svg)](https://github.com/gilsegura/psr-messages/actions/workflows/tests.yaml)
[![codecov](https://codecov.io/github/gilsegura/psr-messages/graph/badge.svg)](https://codecov.io/github/gilsegura/psr-messages)
[![static analysis](https://github.com/gilsegura/psr-messages/actions/workflows/static-analysis.yaml/badge.svg)](https://github.com/gilsegura/psr-messages/actions/workflows/static-analysis.yaml)
[![coding standards](https://github.com/gilsegura/psr-messages/actions/workflows/coding-standards.yaml/badge.svg)](https://github.com/gilsegura/psr-messages/actions/workflows/coding-standards.yaml)

Framework-agnostic toolkit for parsing, validating and producing HTTP messages
on top of PSR-7 and PSR-15, with first-class support for JSON and JSON:API. It
turns a raw request body, headers and query parameters into typed, validated
schema objects, and builds JSON / JSON:API responses and error documents. The
`gilsegura/psr-messages-bundle` package wires these middlewares into Symfony.

The guiding principle: **the fixed, abstract structure lives here; the concrete
shapes live in the consuming library**. This toolkit owns the JSON:API formats
(how a resource, a relationship, a link, a query parameter is shaped and parsed)
and the abstractions a resource builds on; each application provides one
concretion per resource — its create/update/query/headers schemas and its
resource presenter — typed to that resource.

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

## The request lifecycle

For every request the toolkit runs **validation first, parsing second**:

1. **Validate** the raw body, headers and query against JSON Schema
   (`SchemaValidator`, `HeadersValidator`, `QueryParamsValidator`), surfacing a
   typed exception on failure (`InvalidBodyException`, `InvalidHeadersException`,
   `InvalidQueryException`). What an endpoint allows — which fields, includes,
   sort fields and sparse fieldsets are accepted — is enforced here.
2. **Parse** the now-valid input into typed objects through the middlewares
   below, so handlers read typed schemas instead of raw arrays.

## Schemas

A schema turns raw input into a typed object:

- **`SchemaInterface`** — a serializable type with a static `supports(array $data)`
  guard that discriminates between the shapes an endpoint accepts, so the right
  schema is chosen and deserialized into a typed object. `supports()` receives the
  same already-validated, typed data as `deserialize()` (validation happened in
  step 1), so it only discriminates — it never re-checks integrity. Input only:
  `serialize()` is unsupported.
- **`SchemaResolverInterface` / `SchemaResolver`** — pick the first schema whose
  `supports()` matches the validated input, raising `UnresolvedSchemaException`
  when none do.
- **`SingleShapeTrait`** — provides `supports()` returning `true`, for the common
  case of an endpoint with a single shape, removing the repeated guard. A
  single-shape schema uses it and writes only `deserialize()` (plus the input-only
  `serialize()`); a discriminating schema does not use it and writes its own
  `supports()`.

A request body that accepts several shapes (e.g. create vs update) exposes one
schema per shape; the resolver chooses. Each schema reads its fields with
`RequiredReader` (mandatory) and `OptionalReader` (partial updates), the latter
returning an `Optional` that distinguishes "absent, leave untouched" from
"present, write this value".

## Middlewares (PSR-15)

Three middlewares type the parts of a request, after validation:

- **`ParsedBodyMiddleware`** — parses the body with the endpoint's parser and
  stores the resolved schema as the parsed body. Rejects an unsupported content
  type with `UnsupportedMediaTypeException`; an empty body passes through.
- **`ParseHeadersMiddleware`** — resolves the headers schema and stores the typed
  object as a request attribute, keyed by its class.
- **`ParseQueryParamsMiddleware`** — same for query parameters; a request with no
  query parameters passes through untouched.

## JSON:API documents

`Psr\Messages\JsonApi\Document` builds the response side. Every definition takes
the **minimal identity** in its constructor and is composed further through
immutable `withXxx()` methods, so a document is assembled one concern at a time:

- **`ResourceObject`** — `new ResourceObject($type, $id, $attributes)`, then
  `withOneRelationship()`, `withManyRelationship()`, `withLinks()`, `withMeta()`.
- **`ResourceIdentifier`** — the `{type, id}` linkage; `withMeta()` for meta.
- **`ToOneRelationship` / `ToManyRelationship`** — linkage plus optional links
  and meta; `withIdentifier()`, `withLinks()`, `withMeta()`.
- **`SingleResourceDocument` / `ResourceCollectionDocument`** — primary data plus
  `withLinks()` (including pagination), `withMeta()` and `withIncluded()` for
  compound documents.

Resource types and relationship names are polymorphic: `ResourceTypeInterface`
and `RelationshipNameInterface` both extend `\BackedEnum`, so each application
defines its own type and relationship enums while the documents stay generic.
Relationships are added with the typed name — `withOneRelationship($name, ...)`
and `withManyRelationship($name, ...)` take a `RelationshipNameInterface` — and
includes are matched against it (`AbstractIncludes::has($name)`).

### Presenting a resource

`ResourcePresenterInterface<TModel>` is the fixed shape of the output side: one
presenter per resource in the consuming library turns a domain model into a
`ResourceInterface`, honoring the requested sparse fieldsets. Relationships are
passed in by the caller (e.g. a document builder resolving includes), keeping the
presenter focused on the primary resource.

## JSON:API query parameters

`Page` has a fixed structure and is the one query object shared as-is: it parses
`page[number]`/`page[size]` and derives `offset()`. Everything else is
**abstract**, because each resource allows different includes, fields, sort
fields and filters:

- **`AbstractIncludes`** — owns the `include=a,b` format (`has()`, `names()`,
  `isEmpty()`).
- **`AbstractFields`** — owns the `fields[type]=a,b` format (`forType()`,
  `has()`, `apply()` for sparse fieldsets), resolving each type name to a typed
  `ResourceTypeInterface`.
- **`AbstractSort`** — owns the `sort=-a,b` format (ascending/descending).
- **`AbstractFilters`** — owns the `filter[field]=value` format (`forField()`).
- **`AbstractQuerySchema`** — the query-schema machinery: input only, one shape
  per request by default.

A consuming library subclasses these per resource (`ArticleIncludes`,
`ArticleFields`, `ArticleSort`, `ArticleQuery`), reusing the fixed parse format
and only typing the result; the endpoint's JSON Schema enforces what is allowed.

## A resource's concretion

For each resource an application defines a uniform set, all validated before
parsing:

- a **create** request schema (mandatory fields via `RequiredReader`),
- an **update** request schema (partial fields via `OptionalReader` / `Optional`),
- a **query** schema (`extends AbstractQuerySchema`, composing `Page` and the
  resource's typed includes/fields/sort/filters),
- a **headers** schema (the typed headers it needs),
- a **presenter** (`implements ResourcePresenterInterface`).

## JSON

`Psr\Messages\Json` handles plain `application/json` and mirrors the JSON:API
flow without any JSON:API concepts (no resource type, relationships, includes or
sparse fieldsets). `JsonParser` resolves a request body into a typed schema;
`JsonResponseFactory` builds responses. On the output side, `JsonPresenterInterface`
maps a serializable read model to a document payload — the plain-JSON counterpart
of the resource presenter — and `JsonDocument` / `JsonCollectionDocument` render a
single payload or a list of them. `JsonErrorDocument` renders errors.

## Links

`Psr\Messages\Link` models hypermedia links: `Href`, `Link`, and
`LinkTypeInterface` (a `\BackedEnum`) with `LinkType` providing the standard
types (`self`, `related`, `first`, `last`, `prev`, `next`, ...). Downstream
libraries can define their own link types and stay polymorphic.

## Headers

`Psr\Messages\Headers` parses the `Authorization` header into typed credentials:
`BearerToken` and `BasicCredentials` via `ParsesAuthorizationHeaderTrait` and
`AuthorizationScheme`.

## Errors

`Psr\Messages\Error` provides the error model both document formats render:
`ErrorInterface` / `Error`, `ErrorCodeInterface`, and `Source` (with
`SourceTypeInterface` / `SourceType`) pointing at the request member that caused
the error. `JsonErrorDocument` and `JsonApiErrorDocument` render them for each
media type.

## A complete flow

The pieces fit into one request/response flow. The fixed, abstract parts below
live in this package; the typed concretions (`ArticleQuery`, `ArticlePresenter`,
the type and relationship enums, the JSON Schemas) live in the consuming library.

A read endpoint, `GET /articles?include=author&fields[articles]=title&page[number]=1`:

1. **Validate, then parse the query.** `ParseQueryParamsMiddleware` first runs
   `QueryParamsValidator` against the endpoint's JSON Schema (which enforces the
   allowed includes, fields and sort), then resolves the typed query:

   ```php
   final readonly class ArticleQuery extends AbstractQuerySchema
   {
       public function __construct(
           public Page $page,
           public ArticleIncludes $includes,
           public ArticleFields $fields,
           public ArticleSort $sort,
       ) {
       }

       public static function deserialize(array $attributes): static
       {
           return new self(
               Page::deserialize($attributes),
               ArticleIncludes::deserialize($attributes),
               ArticleFields::deserialize($attributes),
               ArticleSort::deserialize($attributes),
           );
       }
   }
   ```

2. **Drive the read side from the typed query.** The handler reads
   `$query->page->offset()`/`->size`, turns `$query->sort` into an order
   (`$query->sort->directionFor('created')`), `$query->filters->forField(...)`
   into criteria, and resolves includes with `$query->includes->has(Article::AUTHOR)`
   and `forName()` (whose `Path::tail()` drives nested includes). It returns
   serializable read models.

3. **Present each model.** `ArticlePresenter` (an `ResourcePresenterInterface`)
   turns a read model into a resource, applying the sparse fieldsets and attaching
   the relationships the handler resolved:

   ```php
   public function present(SerializableInterface $model, AbstractFields $fields, array $relationships = []): ResourceInterface
   {
       return (new ResourceObject(ArticleType::ARTICLE, $model->id(), new ArticleAttributes($fields->apply(ArticleType::ARTICLE, $model->serialize()))))
           ->withOneRelationship(Article::AUTHOR, $relationships['author']);
   }
   ```

4. **Build the document and respond.** The presented resources go into a
   `ResourceCollectionDocument` with pagination links and meta, and
   `JsonApiResponseFactory::collection($document, Status::OK)` produces the
   PSR-7 response.

A write endpoint, `POST /articles`, is the mirror image on the input side:
`ParsedBodyMiddleware` validates the body with `SchemaValidator`, then resolves
a typed `CreateArticleRequest` (`SchemaInterface`, fields read with
`RequiredReader`); the handler executes the command; the created resource is
presented and returned with `JsonApiResponseFactory::single(...)`. A `PATCH`
differs only in using `OptionalReader` / `Optional` so absent fields are left
untouched. Failures anywhere surface as typed exceptions that
`JsonApiResponseFactory::error(...)` renders as a JSON:API error document.

The plain-JSON flow is identical with the `Json` pieces: `JsonParser`,
`JsonPresenterInterface`, `JsonDocument` / `JsonCollectionDocument` and
`JsonResponseFactory`, minus the JSON:API-only concerns.

## License

MIT. See [LICENSE](LICENSE).
