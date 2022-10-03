<?php declare(strict_types = 1);

namespace ZohoCrmConnector\Auth;

/**
 * Interface for token storages.
 */
interface TokenStorageInterface
{
    public function load(): ?AccessToken;

    public function save(AccessToken $data): void;
}
