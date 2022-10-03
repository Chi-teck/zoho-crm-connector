<?php declare(strict_types = 1);

namespace ZohoCrmConnector\Tests\Auth\Storage;

use PHPUnit\Framework\TestCase;
use ZohoCrmConnector\Auth\AccessToken;
use ZohoCrmConnector\Auth\Storage\NullStorage;

/**
 * A test for NullStorage.
 */
final class NullStorageTest extends TestCase
{
    public function testFileStorage(): void
    {
        $storage = new NullStorage();
        $token = new AccessToken('https://example.com', 3600, '123', '456');
        $storage->save($token);
        self::assertNull($storage->load());
    }
}
