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
    /**
     * @readonly
     * @var \GuzzleHttp\Client
     */
    private $client;
    /**
     * @readonly
     * @var \ZohoCrmConnector\Auth\AccessTokenProvider
     */
    private $tokenProvider;
    public function __construct(AccessTokenProvider $tokenProvider)
    {
        $this->tokenProvider = $tokenProvider;
    }

    public function getClient(): Client
    {
        if (!isset($this->client)) {
            $this->client = $this->createClient();
        }
        return $this->client;
    }

    public function createClient(array $config = []): Client
    {
        $config['handler'] = $config['handler'] ?? HandlerStack::create();

        $access_token = $this->tokenProvider->getToken();

        $config['handler']->push(Middleware::mapRequest([$access_token, 'signRequest']));
        $config['handler']->push(Middleware::mapResponse([ZohoResponse::class, 'createFromResponse']));

        $config['base_uri'] = self::buildBaseUri($access_token);
        return new Client($config);
    }

    /**
     * Proxy some Guzzle request methods.
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        // Add a little syntactic sugar to make the input structure a bit
        // smaller.
        if (isset($arguments[1]['data'])) {
            $arguments[1]['json']['data'] = $arguments[1]['data'];
            unset($arguments[1]['data']);
        }
        switch ($name) {
            case 'get':
            case 'head':
            case 'post':
            case 'put':
            case 'patch':
            case 'delete':
                $method = $name;
                break;
            default:
                throw new \InvalidArgumentException('Unsupported Guzzle method.');
        }
        return $this->getClient()->{$method}(...$arguments);
    }

    private function buildBaseUri(AccessToken $access_token): string
    {
        return $access_token->apiDomain . '/crm/v3/';
    }
}
