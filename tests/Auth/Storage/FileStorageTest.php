<?php declare(strict_types = 1);

namespace ZohoCrmConnector\Tests\Auth\Storage;

use PHPUnit\Framework\TestCase;
use ZohoCrmConnector\Auth\AccessToken;
use ZohoCrmConnector\Auth\Storage\FileStorage;

/**
 * A test for FileStorage.
 */
final class FileStorageTest extends TestCase
{
    public function testFileStorage(): void
    {
        $file_name = \sys_get_temp_dir() . '/token-test.' . \mt_rand();
        $storage = new FileStorage($file_name);

        $token = new AccessToken('https://example.com', 3600, '123', '456');
        $storage->save($token);

        $stored_token = $storage->load();
        self::assertEquals($token, $stored_token);

        $directory = \sys_get_temp_dir() . '/not/existing/directory';
        self::expectExceptionObject(
            new \RuntimeException(\sprintf('Directory "%s" does not exist.', $directory)),
        );
        new FileStorage($directory . '/token-test');
    }
}
