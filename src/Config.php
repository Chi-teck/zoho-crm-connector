<?php declare(strict_types = 1);

namespace ZohoCrmConnector;

/**
 * API credentials.
 */
final class Config
{
    public function __construct(
        public readonly string $domain,
        public readonly string $clientId,
        public readonly string $clientSecret,
        public readonly string $authToken,
    ) {}
}
