<?php declare(strict_types = 1);

namespace ZohoCrmConnector;

/**
 * API credentials.
 */
final class Config
{
    /**
     * @readonly
     * @var string
     */
    public $domain;
    /**
     * @readonly
     * @var string
     */
    public $clientId;
    /**
     * @readonly
     * @var string
     */
    public $clientSecret;
    /**
     * @readonly
     * @var string
     */
    public $authToken;
    public function __construct(string $domain, string $clientId, string $clientSecret, string $authToken)
    {
        $this->domain = $domain;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->authToken = $authToken;
    }
}
