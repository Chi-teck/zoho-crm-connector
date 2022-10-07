<?php declare(strict_types = 1);

namespace ZohoCrmConnector\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use PHPUnit\Framework\TestCase;
use ZohoCrmConnector\Auth\AccessToken;
use ZohoCrmConnector\Auth\AccessTokenProvider;
use ZohoCrmConnector\Auth\Storage\MemoryStorage;
use ZohoCrmConnector\Config;
use ZohoCrmConnector\Connector;

/**
 * A test for ZOHO connector.
 */
final class ConnectorTest extends TestCase
{
    public function testCreateClient(): void
    {
        $connector = self::createConnector(new MemoryStorage());

        $handlerStack = new HandlerStack(new ZohoHandler());
        $handlerStack->push(Middleware::httpErrors());
        $client = $connector->createClient(['handler' => $handlerStack]);

        $response = $client->get('/Leads');
        $expected_result['data'] = [
            ['id' => 101],
            ['id' => 102],
            ['id' => 103],
        ];
        self::assertSame(\json_encode($expected_result), (string) $response->getBody());
    }

    public function testGetAndDeleteToken(): void
    {
        $storage = new MemoryStorage();
        $connector = self::createConnector($storage);

        $token = $connector->getToken();
        self::assertInstanceOf(AccessToken::class, $token);

        $connector->deleteToken();
        self::assertNull($storage->load());
    }

    private static function createConnector(MemoryStorage $storage): Connector
    {
        $handlerStack = new HandlerStack(new ZohoHandler());
        $handlerStack->push(Middleware::httpErrors());
        $client = new Client(['handler' => $handlerStack]);

        $config = new Config(
            domain: 'https://accounts.zoho.com',
            clientId: 'CLIENT_ID',
            clientSecret: 'CLIENT_SECRET',
            authToken: 'AUTH_TOKEN',
        );
        $token_provider = new AccessTokenProvider($config, $storage, $client);
        return new Connector($token_provider);
    }
}
