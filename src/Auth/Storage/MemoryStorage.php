<?php declare(strict_types = 1);

namespace ZohoCrmConnector\Auth\Storage;

use ZohoCrmConnector\Auth\AccessToken;

/**
 * Implements a storage for tokens using memory.
 */
final class MemoryStorage implements TokenStorageInterface
{
    private ?AccessToken $data = null;

    public function load(): ?AccessToken
    {
        return $this->data;
    }

    public function save(AccessToken $token): void
    {
        $this->data = clone $token;
    }
}