<?php declare(strict_types = 1);

namespace ZohoCrmConnector\Tests\Auth;

use PHPUnit\Framework\TestCase;
use ZohoCrmConnector\Auth\AccessToken;

/**
 * A test for Config.
 */
final class AccessTokenTest extends TestCase
{
    public function testHasExpired(): void
    {
        $token = new AccessToken('https://example.com', 3600, '123', null);
        self::assertFalse($token->hasExpired());

        // The method has 'expire offset' of 30 seconds. So that this token
        // should be already expired.
        $token = new AccessToken('https://example.com', 30, '123', null);
        self::assertTrue($token->hasExpired());
    }
}
