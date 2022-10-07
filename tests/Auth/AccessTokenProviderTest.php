<?php declare(strict_types = 1);

namespace ZohoCrmConnector\Tests\Auth;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use PHPUnit\Framework\TestCase;
use ZohoCrmConnector\Auth\AccessToken;
use ZohoCrmConnector\Auth\AccessTokenProvider;
use ZohoCrmConnector\Auth\AuthException;
use ZohoCrmConnector\Auth\Storage\MemoryStorage;
use ZohoCrmConnector\Config;
use ZohoCrmConnector\Tests\ZohoHandler;

/**
 * A test for FileStorage.
 */
final class AccessTokenProviderTest extends TestCase
{
    public function testCreateToken(): void
    {
        $storage = new MemoryStorage();

        $provider = new AccessTokenProvider(self::createConfig(), $storage, self::createClient());
        $token = $provider->getToken();

        self::assertInstanceOf(AccessToken::class, $token);
        self::assertSame('https://api.example.com', $token->apiDomain);
        self::assertSame('ACCESS_TOKEN_1', $token->accessToken);
        self::assertSame('REFRESH_TOKEN', $token->refreshToken);

        // Check if the token has been stored.
        self::assertEquals($token, $storage->load());
    }

    public function testRefreshToken(): void
    {
        $storage = new MemoryStorage();
        $expired_token = new AccessToken('https://api.example.com', 0, 'ACCESS_TOKEN_1', 'REFRESH_TOKEN');
        $storage->save($expired_token);

        $provider = new AccessTokenProvider(self::createConfig(), $storage, self::createClient());
        $token = $provider->getToken();

        self::assertInstanceOf(AccessToken::class, $token);
        self::assertSame('https://api.example.com', $token->apiDomain);
        self::assertSame('ACCESS_TOKEN_2', $token->accessToken);
        self::assertSame('REFRESH_TOKEN', $token->refreshToken);

        // Check if the token has been stored.
        self::assertEquals($token, $storage->load());
    }

    public function testDeleteToken(): void
    {
        $storage = new MemoryStorage();

        $provider = new AccessTokenProvider(self::createConfig(), $storage, self::createClient());
        $token = $provider->getToken();

        self::assertInstanceOf(AccessToken::class, $token);

        // Check if the token has been stored.
        self::assertEquals($token, $storage->load());

        $provider->deleteToken();
        self::assertNull($storage->load());
    }

    public function testErrors(): void
    {
        $storage = new MemoryStorage();
        $config = new Config('https://example.com', 'CLIENT_ID', 'WRONG_SECRET', 'AUTH_TOKEN');
        $provider = new AccessTokenProvider($config, $storage, self::createClient());
        self::expectExceptionObject(new AuthException('wrong_client'));
        $provider->getToken();
    }

    private static function createConfig(): Config
    {
        return new Config('https://example.com', 'CLIENT_ID', 'CLIENT_SECRET', 'AUTH_TOKEN');
    }

    private static function createClient(): Client
    {
        $handlerStack = new HandlerStack(new ZohoHandler());
        $handlerStack->push(Middleware::httpErrors());
        return new Client(['handler' => $handlerStack]);
    }
}
