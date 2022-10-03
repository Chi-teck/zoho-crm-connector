<?php declare(strict_types = 1);

namespace ZohoCrmConnector\Tests;

use PHPUnit\Framework\TestCase;
use ZohoCrmConnector\Config;

/**
 * A test for Config.
 */
final class ConfigTest extends TestCase
{
    public function testConstructor(): void
    {
        $config = new Config('https://example.com', '123', '456', '789');
        self::assertSame('https://example.com', $config->domain);
        self::assertSame('123', $config->clientId);
        self::assertSame('456', $config->clientSecret);
        self::assertSame('789', $config->authToken);
    }
}
