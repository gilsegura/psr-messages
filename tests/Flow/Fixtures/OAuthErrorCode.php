<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Flow\Fixtures;

use Psr\Messages\Error\Definition\ErrorCodeInterface;

/**
 * OAuth2 error codes (RFC 6749 §5.2), as a downstream-defined error code enum.
 */
enum OAuthErrorCode: string implements ErrorCodeInterface
{
    case INVALID_REQUEST = 'invalid_request';
    case INVALID_CLIENT = 'invalid_client';
    case INVALID_GRANT = 'invalid_grant';
    case UNSUPPORTED_GRANT_TYPE = 'unsupported_grant_type';
}
