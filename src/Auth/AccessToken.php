<?php declare(strict_types = 1);

namespace ZohoCrmConnector\Auth;

use Psr\Http\Message\RequestInterface;

/**
 * A simple data structure to represent Zoho access token.
 */
final class AccessToken implements \Stringable
{
    private const AUTH_HEADER_PREFIX = 'Zoho-oauthtoken';
    private const TOKEN_EXPIRE_OFFSET = 30;
    private readonly int $expires;

    public function __construct(
        public readonly string $apiDomain,
        int $expiresIn,
        public readonly string $accessToken,
        public readonly ?string $refreshToken,
    ) {
        $this->expires = \time() + $expiresIn;
    }

    /**
     * Apply time offset so the token expires a bit earlier for safety.
     */
    public function hasExpired(): bool
    {
        return $this->expires - \time() - self::TOKEN_EXPIRE_OFFSET <= 0;
    }

    public function signRequest(RequestInterface $request): RequestInterface
    {
        return $request->withHeader('Authorization', self::AUTH_HEADER_PREFIX . ' ' . $this);
    }

    public function __toString(): string
    {
        return $this->accessToken;
    }
}
