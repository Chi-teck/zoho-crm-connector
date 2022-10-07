<?php declare(strict_types=1);

namespace ZohoCrmConnector;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use ZohoCrmConnector\Auth\AccessToken;
use ZohoCrmConnector\Auth\AccessTokenProvider;

/**
 * Zoho CRM connector.
 *
 * @method \ZohoCrmConnector\ZohoResponse get(string $uri, array $options = [])
 * @method \ZohoCrmConnector\ZohoResponse post(string $uri, array $options = [])
 * @method \ZohoCrmConnector\ZohoResponse put(string $uri, array $options = [])
 * @method \ZohoCrmConnector\ZohoResponse patch(string $uri, array $options = [])
 * @method \ZohoCrmConnector\ZohoResponse delete(string $uri, array $options = [])
 */
final class Connector
{
    private readonly Client $client;

    public function __construct(
        private readonly AccessTokenProvider $tokenProvider,
    ) {}

    public function getClient(): Client
    {
        if (!isset($this->client)) {
            $this->client = $this->createClient();
        }
        return $this->client;
    }

    public function createClient(array $config = []): Client
    {
        $config['handler'] ??= HandlerStack::create();

        $access_token = $this->tokenProvider->getToken();

        $config['handler']->push(Middleware::mapRequest([$access_token, 'signRequest']));
        $config['handler']->push(Middleware::mapResponse([ZohoResponse::class, 'createFromResponse']));

        $config['base_uri'] = self::buildBaseUri($access_token);
        return new Client($config);
    }

    /**
     * This method might be helpful to check auth status.
     */
    public function getToken(): AccessToken
    {
        return $this->tokenProvider->getToken();
    }

    public function deleteToken(): void
    {
        $this->tokenProvider->deleteToken();
    }

    /**
     * Proxy some Guzzle request methods.
     */
    public function __call(string $name, array $arguments): mixed
    {
        // Add a little syntactic sugar to make the input structure a bit
        // smaller.
        if (isset($arguments[1]['data'])) {
            $arguments[1]['json']['data'] = $arguments[1]['data'];
            unset($arguments[1]['data']);
        }
        $method = match ($name) {
            'get', 'head', 'post', 'put', 'patch', 'delete' => $name,
            default => throw new \InvalidArgumentException('Unsupported Guzzle method.'),
        };
        return $this->getClient()->{$method}(...$arguments);
    }

    private function buildBaseUri(AccessToken $access_token): string
    {
        return $access_token->apiDomain . '/crm/v3/';
    }
}
