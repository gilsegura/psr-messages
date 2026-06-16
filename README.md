# PSR MESSAGES COMPONENT

[![tests](https://github.com/gilsegura/psr-messages/actions/workflows/tests.yaml/badge.svg)](https://github.com/gilsegura/psr-messages/actions/workflows/tests.yaml)
[![codecov](https://codecov.io/github/gilsegura/psr-messages/graph/badge.svg)](https://codecov.io/github/gilsegura/psr-messages)
[![static analysis](https://github.com/gilsegura/psr-messages/actions/workflows/static-analysis.yaml/badge.svg)](https://github.com/gilsegura/psr-messages/actions/workflows/static-analysis.yaml)
[![coding standards](https://github.com/gilsegura/psr-messages/actions/workflows/coding-standards.yaml/badge.svg)](https://github.com/gilsegura/psr-messages/actions/workflows/coding-standards.yaml)

Media-type-aware PSR-7 message parsing and rendering. It turns a validated
request into typed input objects and turns your domain output into a correctly
shaped response body, for both plain JSON and [JSON:API](https://jsonapi.org)
(v1.1). It is framework-agnostic and integrates through PSR-15 middleware.

## Design

The library has a single, deliberately narrow job: **parse to consistent, typed
data on the way in, and serialize typed data on the way out.** It does not
verify business rules and it does not validate structure.

That separation matters, so it is worth stating plainly:

- **Structure validation happens first**, against a JSON Schema, using
  [`gilsegura/psr-validator`](https://github.com/gilsegura/psr-validator) (which
  wraps `opis/json-schema`). By the time this library parses a request, the
  shape is already known to be valid.
- **Parsing/typing happens next.** This library reads the validated input and
  builds typed value objects from it. Because the structure is guaranteed, the
  parsers assume it: a field the schema marks required is read directly, and an
  impossible state throws rather than producing an empty value.
- **The application decides.** The library gives you tools — typed requests,
  resource documents, error documents — and your handler decides what to do with
  them. For example, the library can express an `included` section, but whether
  your `included` resources are fully linked is your handler's call.

Everything is `final readonly`, immutable, and built for PHP 8.4 under PHPStan
`max` with strict rules.

## Installation

```bash
composer require gilsegura/psr-messages
```

Requires PHP 8.4+. It builds on the sibling packages `gilsegura/psr-server`,
`gilsegura/psr-validator` and `gilsegura/serializer`, and on any PSR-7
implementation (the examples use `nyholm/psr7`).

## The two flows

The clearest way to understand the library is to follow a request from the wire
to the response. The two flows below are mirrored exactly by the integration
tests in `tests/Flow`, so they are guaranteed to stay correct.

### Flow 1 — OAuth2 client credentials (plain JSON)

A `POST /token` request authenticates a client with HTTP Basic and asks for a
token. Headers and body are validated against JSON Schemas, the credentials and
the grant request are parsed into typed objects, and the issued token is
rendered as a plain JSON response.

```php
use Psr\Messages\Json\Document\JsonDocument;
use Psr\Messages\Json\JsonResponseFactory;
use Psr\Messages\Message\HeadersValidator;
use Psr\Messages\Message\SchemaValidator;
use Psr\Messages\Schema\SchemaResolver;
use Psr\Server\ResponseFactory\ResponseFactory;
use Psr\Server\ResponseFactory\Status;
use Psr\Validator\Schema\SchemaValidator as OpisSchemaValidator;
use Psr\Validator\SchemaFactory\RawFactory;

// 1. INPUT — a PSR-7 request as it arrives on the wire.
$authorization = 'Basic '.base64_encode('service-worker:s3cr3t');
$request = new ServerRequest(
    'POST',
    'https://api.example.com/token',
    ['Content-Type' => 'application/json', 'Authorization' => $authorization],
    '{"grant_type":"client_credentials","scope":"posts:read posts:write"}',
);

// 2. VALIDATION — headers, then body, against their JSON Schemas.
$opis = new OpisSchemaValidator();
$request = (new HeadersValidator($opis, new RawFactory($headersSchema)))($request);
$request = (new SchemaValidator($opis, new RawFactory($bodySchema)))($request);

// 3. PARSING — typed objects from the validated input.
$credentials = (new SchemaResolver(BasicAuthCredentials::class))
    ->resolve(['authorization' => $request->getHeaderLine('Authorization')]);
// $credentials->credentials->username === 'service-worker'

$tokenRequest = (new SchemaResolver(ClientCredentialsRequest::class))
    ->resolve(json_decode((string) $request->getBody(), true));
// $tokenRequest->grantType === 'client_credentials'
// $tokenRequest->scopes === ['posts:read', 'posts:write']

// 4. HANDLER — your application issues the token.
$token = new AccessToken('opaque-token-value', expiresIn: 3600, scope: 'posts:read posts:write');

// 5. OUTPUT — rendered as a plain JSON response.
$factory = new JsonResponseFactory(new ResponseFactory($psr17, $psr17));
$response = $factory->document(new JsonDocument($token), Status::OK);
```

The response body is exactly:

```json
{
    "access_token": "opaque-token-value",
    "token_type": "Bearer",
    "expires_in": 3600,
    "scope": "posts:read posts:write"
}
```

Note where the work lives. Extracting and base64-decoding the `Authorization`
header is the schema's job — `BasicAuthCredentials` uses the shared
`ParsesAuthorizationHeaderTrait` and `DecodesBasicCredentialsTrait` to turn the
header line into a `BasicCredentials` value object. Verifying that the client
and secret are real is the authentication layer's job, not this library's.

### Flow 2 — A JSON:API blog (resources, relationships, errors)

A JSON:API endpoint that creates and lists posts. Posts have an **author**
(to-one) and **comments** (to-many). Everything runs through a real PSR-15
pipeline.

**Creating a post.** The pipeline validates the body, then parses it into a
typed `CreatePostRequest`; the handler persists and renders a single resource
document with the author included.

```php
use Psr\Messages\JsonApi\JsonApiParser;
use Psr\Messages\JsonApi\JsonApiResponseFactory;
use Psr\Messages\JsonApi\Document\SingleResourceDocument;
use Psr\Messages\Middleware\ParsedBodyMiddleware;
use Psr\Messages\Schema\SchemaResolver;
use Psr\Server\Middleware\MiddlewareRunner;
use Psr\Server\RequestHandler;
use Psr\Server\ResponseFactory\Status;

$pipeline = new MiddlewareRunner(
    new ValidatingMiddleware($bodyValidator),                       // structure first
    new ParsedBodyMiddleware(new JsonApiParser(                     // then typing
        new SchemaResolver(CreatePostRequest::class),
    )),
);

$handler = new RequestHandler(function ($request) use ($factory, $presenter, $repository) {
    $createPost = $request->getParsedBody();                        // typed CreatePostRequest
    $post = $repository->create($createPost->title, $createPost->body, $createPost->authorId);

    $document = (new SingleResourceDocument($presenter->resource($post)))
        ->withIncluded(...$presenter->included($post))
        ->withLinks(new Link(LinkType::SELF, new Href('https://api.example.com/posts/'.$post->id)));

    return $factory->single($document, Status::CREATED);
});

$response = $pipeline($request, $handler);
```

The rendered document carries data, relationships, links at both the document
and resource level, an `included` section, and the `jsonapi` member:

```json
{
    "data": {
        "type": "posts",
        "id": "p-1",
        "attributes": { "title": "Hello world", "body": "My first post." },
        "relationships": {
            "author": {
                "data": { "type": "authors", "id": "a-1" },
                "links": {
                    "self": "https://api.example.com/posts/p-1/relationships/author",
                    "related": "https://api.example.com/posts/p-1/author"
                }
            }
        },
        "links": { "self": "https://api.example.com/posts/p-1" }
    },
    "included": [
        { "type": "authors", "id": "a-1", "attributes": { "name": "Ada Lovelace" } }
    ],
    "links": { "self": "https://api.example.com/posts/p-1" },
    "jsonapi": { "version": "1.1" }
}
```

**Listing posts.** A `GET /posts` request carries query parameters. The
`ParseQueryParamsMiddleware` turns them into a typed `JsonApiQuerySchema`, which
drives what the handler fetches; the result is a resource collection document
with pagination links and meta.

```php
use Psr\Messages\JsonApi\Query\JsonApiQuerySchema;
use Psr\Messages\JsonApi\Document\ResourceCollectionDocument;
use Psr\Messages\Middleware\ParseQueryParamsMiddleware;

$request = (new ServerRequest('GET', 'https://api.example.com/posts'))
    ->withQueryParams([
        'sort'    => '-created,title',
        'page'    => ['number' => '1', 'size' => '2'],
        'filter'  => ['status' => 'published'],
        'fields'  => ['posts' => 'title', 'authors' => 'name'],
        'include' => 'author,comments',
    ]);

$pipeline = new MiddlewareRunner(
    new ParseQueryParamsMiddleware(new SchemaResolver(JsonApiQuerySchema::class)),
);

$handler = new RequestHandler(function ($request) use ($factory, $presenter, $repository) {
    $query = $request->getAttribute(JsonApiQuerySchema::class);     // typed query
    $posts = $repository->page($query->page->number, $query->page->size);

    $document = (new ResourceCollectionDocument(array_map($presenter->resource(...), $posts)))
        ->withIncluded(...$included)
        ->withLinks(
            new Link(LinkType::SELF, new Href('https://api.example.com/posts?page[number]=1')),
            new Link(LinkType::NEXT, new Href('https://api.example.com/posts?page[number]=2')),
        )
        ->withMeta(['total' => $repository->total()]);

    return $factory->collection($document, Status::OK);
});
```

**Errors.** When validation fails, the thrown exception is rendered as a
JSON:API error document. Validation exceptions carry their errors and know how
to build the right `source` (a JSON Pointer for the body, a parameter name for
the query, a header name for headers).

```php
try {
    $bodyValidator($request);
} catch (\Throwable $throwable) {
    return $factory->error($throwable, Status::UNPROCESSABLE_CONTENT);
}
```

```json
{
    "errors": [
        {
            "code": "malformed_content",
            "title": "Invalid body",
            "detail": "The property title is required",
            "source": { "pointer": "/data/attributes/title" }
        }
    ],
    "jsonapi": { "version": "1.1" }
}
```

**Partially updating a post (PATCH).** Unlike a creation, a `PATCH` body has no
fixed shape: it carries only the fields that change. The JSON Schema validates
that whatever is present has the right type, without requiring any field. The
typed `UpdatePostRequest` represents each updatable field as an `Optional`, so
the handler can tell *left untouched* (absent) from *set to this value*
(present) — a distinction a nullable type cannot make.

```php
use Psr\Messages\Tests\Flow\Fixtures\Blog\UpdatePostRequest;

// body: {"data":{"type":"posts","id":"p-1","attributes":{"title":"Edited title"}}}

$handler = new RequestHandler(function ($request) use ($factory, $presenter, $repository) {
    $update = $request->getParsedBody();          // typed UpdatePostRequest

    // only "title" was sent
    $update->title->isPresent();                  // true
    $update->body->isPresent();                   // false  -> leave untouched
    $update->authorId->isPresent();               // false

    $post = $repository->update($update->id, $update->title, $update->body, $update->authorId);

    return $factory->single(new SingleResourceDocument($presenter->resource($post)), Status::OK);
});
```

The handler applies only the present fields; `body` and the author are left as
they were. Because the structure was already validated, a present field is read
directly — there is no re-checking of types in the request object, and an
impossible state (a value that validation should have rejected) throws an
`UnexpectedStateException` rather than being silently coerced.


## What the library gives you

### Input: schemas and parsers

A `SchemaInterface` is a self-resolving description of one shape of incoming
data. It declares whether raw input is its own (`supports`) and, being
serializable, turns that data into a typed object (`deserialize`). An endpoint
that accepts several shapes exposes one schema per shape; a `SchemaResolver`
picks the first whose `supports` matches.

Parsers (`JsonApiParser`, `JsonParser`) decode a request body and leave the
resolved, typed schema object in the parsed body. The middlewares
(`ParsedBodyMiddleware`, `ParseQueryParamsMiddleware`, and the header/query/body
validators) wire this into a PSR-15 pipeline.

### Output: documents and response factories

For JSON:API, build a `SingleResourceDocument` or a `ResourceCollectionDocument`
from `ResourceObject`s. A resource carries its `type`, `id`, attributes,
relationships (`ToOneRelationship`, `ToManyRelationship`), links and meta. Every
piece serializes itself, so a document is just an orchestration of
`serialize()` calls. The JSON:API query side covers all five members: `page`,
`sort`, `filter`, `include` and sparse `fields`.

For plain JSON, wrap any serializable payload in a `JsonDocument`.

Responses are built by media-type-specific factories. `JsonApiResponseFactory`
exposes `single()`, `collection()` and `error()`; `JsonResponseFactory` exposes
`document()` and `error()`. Each sets the right `Content-Type` and is typed to
the documents its media type renders.

### Errors

`Error` value objects describe a problem with a `code`, `title`, `detail`, and
an optional `source`, `links` and `meta`. `JsonApiErrorDocument` and
`JsonErrorDocument` render a throwable: a validation exception that implements
`SourcedValidationExceptionInterface` is expanded into one error per validation
failure, each with its own source; any other throwable becomes a single
internal error.

## Extending it

The polymorphic pieces are interfaces you implement with string-backed enums in
your own domain:

- `ResourceTypeInterface` — your JSON:API resource types (`posts`, `authors`…).
- `ErrorCodeInterface` — your error codes.
- `SchemaInterface` — one per request shape you accept.

Define your attributes as small serializable value objects with a concrete
array shape, and a presenter that maps your domain models to `ResourceObject`s.
The flow tests' fixtures (`tests/Flow/Fixtures`) are a complete, working
example you can copy from.

### Reading a validated body

Because structure is validated upstream, a schema's `deserialize` reads the
decoded body assuming it is correct. Two `Support` helpers make that reading
explicit and keep the impossible-state handling in one place:

- **`RequiredReader`** — for fields the schema guarantees present (a creation).
  `RequiredReader::string($data, 'title')` returns the string or throws
  `UnexpectedStateException` if it is missing or mistyped (an impossible state).
- **`OptionalReader`** — for fields that may or may not be present (a partial
  update). `OptionalReader::string($data, 'title')` returns an `Optional<string>`:
  absent when the field is missing, present otherwise. A present-but-mistyped
  value throws.

Both expose `string()`, `int()` and `nested()` (and `OptionalReader` adds
`bool()`), so navigating a decoded body stays free of repeated `is_*`/`??`
guards while still failing loudly on an impossible state.

```php
// creation: every field is required
$data = RequiredReader::nested($attributes, 'data');
$attrs = RequiredReader::nested($data, 'attributes');
$title = RequiredReader::string($attrs, 'title');           // string

// partial update: fields are optional
$title = OptionalReader::string($attrs, 'title');           // Optional<string>
```

## Testing

```bash
composer install
vendor/bin/phpunit
```

The suite is integration-first: no mocks. The unit tests cover the value
objects, query parsing, serialization, errors and middlewares; the flow tests
run the two scenarios above end to end through a real pipeline.

## License

MIT. See [LICENSE](LICENSE).