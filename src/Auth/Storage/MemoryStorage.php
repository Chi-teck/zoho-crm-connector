<?php declare(strict_types = 1);

namespace ZohoCrmConnector\Auth\Storage;

use ZohoCrmConnector\Auth\AccessToken;

/**
 * Implements a storage for tokens using memory.
 */
final class MemoryStorage implements TokenStorageInterface
{
    private ?AccessToken $data = null;

    /**
     * {@inheritdoc}
     */
    public function load(): ?AccessToken
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function save(AccessToken $token): void
    {
        $this->data = clone $token;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(): void
    {
        $this->data = null;
    }
}
