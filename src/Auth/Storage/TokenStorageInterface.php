<?php declare(strict_types = 1);

namespace ZohoCrmConnector\Auth\Storage;

use ZohoCrmConnector\Auth\AccessToken;

/**
 * Interface for token storages.
 */
interface TokenStorageInterface
{
    /**
     * Loads the token from storage.
     */
    public function load(): ?AccessToken;

    /**
     * Saves the token to strage.
     */
    public function save(AccessToken $token): void;

    /**
     * Deletes the token from storage
     */
    public function delete(): void;
}
