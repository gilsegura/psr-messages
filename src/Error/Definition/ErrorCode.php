<?php

declare(strict_types=1);

namespace Psr\Messages\Error\Definition;

/**
 * The error codes defined by this library. Downstream libraries can define
 * their own string-backed enums implementing ErrorCodeInterface.
 */
enum ErrorCode: string implements ErrorCodeInterface
{
    case MALFORMED_CONTENT = 'malformed_content';
    case MALFORMED_QUERY_PARAM = 'malformed_query_param';
    case MALFORMED_HEADER = 'malformed_header';
    case NOT_ACCEPTABLE_MEDIA_TYPE = 'not_acceptable_media_type';
    case UNSUPPORTED_MEDIA_TYPE = 'unsupported_media_type';
    case INTERNAL_ERROR = 'internal_error';
}
