<?php declare(strict_types = 1);

namespace ZohoCrmConnector\Tests;

use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;

/**
 * Simulates Zoho API server.
 */
final class ZohoHandler
{
    private const CLIENT_ID = 'CLIENT_ID';
    private const CLIENT_SECRET = 'CLIENT_SECRET';
    private const AUTH_TOKEN = 'AUTH_TOKEN';
    private const ACCESS_TOKEN_1 = 'ACCESS_TOKEN_1';
    private const ACCESS_TOKEN_2 = 'ACCESS_TOKEN_2';
    private const REFRESH_TOKEN = 'REFRESH_TOKEN';

    /**
     * Handler callback.
     */
    public function __invoke(RequestInterface $request): FulfilledPromise
    {
        $response = match ($request->getUri()->getPath()) {
            '/oauth/v2/token' => self::authController($request),
            '/Leads' => self::leadsController($request),
            default => new Response(404),
        };
        return new FulfilledPromise($response);
    }

    private static function authController(RequestInterface $request): Response
    {
        // There is some inconsistency in Zoho Authentication.
        // Data for the first auth request should be passed through form data
        // while 'refresh' request is using query string.
        \parse_str((string) $request->getBody(), $form_params);
        $query_string = \explode('?', (string) $request->getUri())[1] ?? '';
        \parse_str($query_string, $query_params);

        // Initial request.
        if (\count($form_params) > 0 && $form_params['grant_type'] === 'authorization_code') {
            $is_valid =
              $form_params['client_id'] === self::CLIENT_ID &&
              $form_params['client_secret'] === self::CLIENT_SECRET &&
              $form_params['code'] === self::AUTH_TOKEN;

            if (!$is_valid) {
                return new Response(body: '{"error": "wrong_client"}');
            }
            $data = [
              'access_token' => self::ACCESS_TOKEN_1,
              'refresh_token' => self::REFRESH_TOKEN,
              'expires_in' => 3600,
              'api_domain' => 'https://api.example.com',
              'token_type' => 'Barier',
            ];
            return new Response(body: \json_encode($data));
        // Refresh request.
        } elseif (\count($query_params) > 0 && $query_params['grant_type'] === 'refresh_token') {
            $is_valid =
                  $query_params['client_id'] === self::CLIENT_ID &&
                  $query_params['client_secret'] === self::CLIENT_SECRET &&
                  $query_params['refresh_token'] === self::REFRESH_TOKEN;
            if (!$is_valid) {
                return new Response(body: '{"error": "wrong_client"}');
            }
            $data = [
                'access_token' => self::ACCESS_TOKEN_2,
                'expires_in' => 3600,
                'api_domain' => 'https://api.example.com',
                'token_type' => 'Barier',
            ];
            return new Response(body: \json_encode($data));
        }

        return new Response(400);
    }

    private static function leadsController(RequestInterface $request): Response
    {
        if ($request->getHeaderLine('Authorization') !== 'Zoho-oauthtoken ACCESS_TOKEN_1') {
            return new Response(400);
        }
        $body['data'] = [
            ['id' => 101],
            ['id' => 102],
            ['id' => 103],
        ];
        return new Response(
            headers: ['Content-Type' => 'application/json'],
            body: \json_encode($body),
        );
    }
}
