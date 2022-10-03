<?php declare(strict_types = 1);

namespace ZohoCrmConnector\Tests\Auth\Storage;

use PHPUnit\Framework\TestCase;
use ZohoCrmConnector\Auth\AccessToken;
use ZohoCrmConnector\Auth\Storage\MemoryStorage;

/**
 * A test for MemoryStorage.
 */
final class MemoryStorageTest extends TestCase
{
    public function testFileStorage(): void
    {
        $storage = new MemoryStorage();

        $token = new AccessToken('https://example.com', 3600, '123', '456');
        $storage->save($token);

        $stored_token = $storage->load();
        self::assertEquals($token, $stored_token);
    }
}
