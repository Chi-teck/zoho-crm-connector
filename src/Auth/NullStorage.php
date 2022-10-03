<?php declare(strict_types = 1);

namespace ZohoCrmConnector\Auth;

/**
 * Dummy token storage.
 */
final class NullStorage implements TokenStorageInterface
{
    public function load(): ?AccessToken
    {
        return null;
    }

    public function save(AccessToken $data): void
    {
        // Intentionally empty.
    }
}
