<?php declare(strict_types = 1);

namespace ZohoCrmConnector\Auth;

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

    public function save(AccessToken $data): void
    {
        $this->data = clone $data;
    }
}
