<?php declare(strict_types = 1);

namespace ZohoCrmConnector\Auth\Storage;

use ZohoCrmConnector\Auth\AccessToken;

/**
 * Dummy token storage.
 */
final class NullStorage implements TokenStorageInterface
{
    public function load(): ?AccessToken
    {
        return null;
    }

    public function save(AccessToken $token): void
    {
        // Intentionally empty.
    }
}
