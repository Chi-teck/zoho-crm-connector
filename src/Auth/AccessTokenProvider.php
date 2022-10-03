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
    public function __construct(
        private readonly Config $config,
        private readonly TokenStorageInterface $storage,
        private readonly Client $client,
        private readonly LoggerInterface $logger = new NullLogger(),
    ) {}

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
        return new AccessToken(
            apiDomain:  $result->api_domain,
            expiresIn: $result->expires_in,
            accessToken: $result->access_token,
            refreshToken: $result->refresh_token,
        );
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
        return new AccessToken(
            apiDomain:  $result->api_domain,
            expiresIn: $result->expires_in,
            accessToken: $result->access_token,
            refreshToken: $token->refreshToken,
        );
    }

    private function buildUrl(): string
    {
        return $this->config->domain . '/oauth/v2/token';
    }

    private function decodeResponse(ResponseInterface $response): mixed
    {
        // @todo Check content type.
        // @todo Check data structure.
        return \json_decode((string) $response->getBody());
    }
}
