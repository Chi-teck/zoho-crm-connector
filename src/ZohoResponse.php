<?php declare(strict_types = 1);

namespace ZohoCrmConnector;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

/**
 * Represents Zoho HTTP Response.
 *
 * Enhances Guzzle responses with `decode()` method.
 */
final class ZohoResponse extends Response
{
    public static function createFromResponse(ResponseInterface $response): self
    {
        return new self(
            $response->getStatusCode(),
            $response->getHeaders(),
            $response->getBody(),
            $response->getProtocolVersion(),
            $response->getReasonPhrase()
        );
    }

    public function decode(): mixed
    {
        $content_type = $this->getHeader('Content-Type')[0] ?? null;
        // Mime type can be followed by 'charset' or 'boundary'.
        // Example: 'application/json; charset=UTF-8'.
        if (!$content_type || \explode(';', $content_type)[0] !== 'application/json') {
            throw new RuntimeException('Only JSON responses can be decoded');
        }
        return \json_decode((string) $this->getBody());
    }
}
