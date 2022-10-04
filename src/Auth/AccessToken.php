<?php declare(strict_types = 1);

namespace ZohoCrmConnector\Auth;

use Psr\Http\Message\RequestInterface;

/**
 * A simple data structure to represent Zoho access token.
 */
final class AccessToken
{
    private const AUTH_HEADER_PREFIX = 'Zoho-oauthtoken';
    private const TOKEN_EXPIRE_OFFSET = 30;
    /**
     * @readonly
     * @var int
     */
    private $expires;
    /**
     * @readonly
     * @var string
     */
    public $apiDomain;
    /**
     * @readonly
     * @var string
     */
    public $accessToken;
    /**
     * @readonly
     * @var string|null
     */
    public $refreshToken;
    public function __construct(string $apiDomain, int $expiresIn, string $accessToken, ?string $refreshToken)
    {
        $this->apiDomain = $apiDomain;
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
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
