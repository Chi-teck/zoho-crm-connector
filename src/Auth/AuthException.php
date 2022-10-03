<?php declare(strict_types = 1);

namespace ZohoCrmConnector\Auth;

use Psr\Http\Client\ClientExceptionInterface;

/**
 * API credentials.
 */
final class AuthException extends \RuntimeException implements ClientExceptionInterface
{
}
