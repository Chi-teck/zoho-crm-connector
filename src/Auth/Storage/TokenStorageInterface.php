<?php declare(strict_types = 1);

namespace ZohoCrmConnector\Auth\Storage;

use ZohoCrmConnector\Auth\AccessToken;

/**
 * Interface for token storages.
 */
interface TokenStorageInterface
{
    public function load(): ?AccessToken;

    public function save(AccessToken $token): void;
}
