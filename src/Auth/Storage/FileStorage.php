<?php declare(strict_types = 1);

namespace ZohoCrmConnector\Auth\Storage;

use ZohoCrmConnector\Auth\AccessToken;

/**
 * Implements a storage for tokens using files.
 */
final class FileStorage implements TokenStorageInterface
{
    public function __construct(
        private readonly string $fileName,
    ) {
        $dir_name = \dirname($this->fileName);
        if (!\file_exists($dir_name) || !\is_dir($dir_name)) {
            throw new \RuntimeException(\sprintf('Directory "%s" does not exist.', $dir_name));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function load(): ?AccessToken
    {
        if (!\file_exists($this->fileName)) {
            return null;
        }

        $fp = \fopen($this->fileName, 'rb');
        \flock($fp, \LOCK_SH);
        $value = \stream_get_contents($fp);
        \flock($fp, \LOCK_UN);
        \fclose($fp);

        return \unserialize($value);
    }

    /**
     * {@inheritdoc}
     */
    public function save(AccessToken $token): void
    {
        \file_put_contents($this->fileName, \serialize($token), \LOCK_EX);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(): void
    {
        if (\file_exists($this->fileName)) {
            \unlink($this->fileName);
        }
    }
}
