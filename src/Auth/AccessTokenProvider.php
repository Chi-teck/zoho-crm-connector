<?php declare(strict_types = 1);

namespace ZohoCrmConnector\Auth;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use ZohoCrmConnector\Auth\Storage\TokenStorageInterface;
use ZohoCrmConnector\Config;

/**
 * Access token provider for Zoho CRM API.
 */
final class AccessTokenProvider
{
    /**
     * @readonly
     * @var \ZohoCrmConnector\Config
     */
    private $config;
    /**
     * @readonly
     * @var \ZohoCrmConnector\Auth\Storage\TokenStorageInterface
     */
    private $storage;
    /**
     * @readonly
     * @var \GuzzleHttp\Client
     */
    private $client;
    /**
     * @readonly
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;
    public function __construct(Config $config, TokenStorageInterface $storage, Client $client, LoggerInterface $logger = null)
    {
        $logger = $logger ?? new NullLogger();
        $this->config = $config;
        $this->storage = $storage;
        $this->client = $client;
        $this->logger = $logger;
    }
    public function getToken(): AccessToken
    {
        $access_token = $this->storage->load();
        if ($access_token) {
            if (!$access_token->hasExpired()) {
                return $access_token;
            }
            $access_token = $this->refreshToken($access_token);
            $this->logger->debug('ZOHO CRM CONNECTOR: Access token has been refreshed.');
        } else {
            $access_token = $this->createToken();
            $this->logger->debug('ZOHO CRM CONNECTOR: Access token has been created.');
        }
        $this->storage->save($access_token);
        return $access_token;
    }

    public function deleteToken(): void
    {
        $this->storage->delete();
    }

    private function createToken(): AccessToken
    {
        $options['form_params'] = [
            'grant_type' => 'authorization_code',
            'client_id' => $this->config->clientId,
            'client_secret' => $this->config->clientSecret,
            'code' => $this->config->authToken,
        ];
        $response = $this->client->post($this->buildUrl(), $options);
        $result = self::decodeResponse($response);
        if (isset($result->error)) {
            throw new AuthException($result->error);
        }
        return new AccessToken($result->api_domain, $result->expires_in, $result->access_token, $result->refresh_token);
    }

    private function refreshToken(AccessToken $token): AccessToken
    {
        $options['query'] = [
            'refresh_token' => $token->refreshToken,
            'client_id' => $this->config->clientId,
            'client_secret' => $this->config->clientSecret,
            'grant_type' => 'refresh_token',
        ];
        $response = $this->client->post($this->buildUrl(), $options);
        $result = self::decodeResponse($response);
        if (isset($result->error)) {
            throw new AuthException($result->error);
        }
        return new AccessToken($result->api_domain, $result->expires_in, $result->access_token, $token->refreshToken);
    }

    private function buildUrl(): string
    {
        return $this->config->domain . '/oauth/v2/token';
    }

    /**
     * @return mixed
     */
    private function decodeResponse(ResponseInterface $response)
    {
        // @todo Check content type.
        // @todo Check data structure.
        return \json_decode((string) $response->getBody());
    }
}
