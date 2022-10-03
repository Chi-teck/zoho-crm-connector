<?php declare(strict_types = 1);

namespace ZohoCrmConnector\Tests;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use ZohoCrmConnector\ZohoResponse;

/**
 * A test for ZohoResponse.
 */
final class ZohoResponseTest extends TestCase
{
    public function testCreateFromResponse(): void
    {
        $response = new Response(
            status: 200,
            headers: ['Content-Type' => 'application/json'],
            body: '{"foo": "bar"}'
        );
        $zoho_response = ZohoResponse::createFromResponse($response);
        self::assertInstanceOf(Response::class, $zoho_response);
        self::assertInstanceOf(ZohoResponse::class, $zoho_response);
        self::assertSame(200, $zoho_response->getStatusCode());
        $headers = ['Content-Type' => ['application/json']];
        self::assertSame($headers, $zoho_response->getHeaders());
        self::assertSame('{"foo": "bar"}', (string) $zoho_response->getBody());
    }

    public function testDecode(): void
    {
        $response = new ZohoResponse(
            headers: ['Content-Type' => 'application/json;charset=UTF-8'],
            body: '{"foo": "bar"}'
        );
        $expected_data = (object) ['foo' => 'bar'];
        self::assertEquals($expected_data, $response->decode());

        $response = new ZohoResponse(
            headers: ['Content-Type' => 'application/json'],
            body: '123'
        );
        self::assertEquals(123, $response->decode());

        $response = new ZohoResponse();
        self::expectExceptionObject(new \RuntimeException('Only JSON responses can be decoded'));
        $response->decode();
    }
}
